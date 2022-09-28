<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use App\Models\Repayment;
use App\Repositories\LoanRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class LoanController extends Controller
{
    /**
     * @var \App\Repositories\LoanRepositoryInterface
     */
    protected LoanRepositoryInterface $repository;

    /**
     * BlockController constructor.
     *
     * @param \App\Repositories\LoanRepositoryInterface $repository
     */
    public function __construct(LoanRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Store block into database.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function create(Request $request) : JsonResponse
    {
        $this->validate($request, [
            'amount' => 'required|numeric|gt:0',
            'terms'  => 'required|numeric|gt:0',
        ]);
        
        $amount = $request->get('amount');
        $terms = $request->get('terms');

        /** @var \App\Models\Loan $loan */
        $loan = $this->repository->create([
            'amount'  => $amount,
            'terms'   => $terms,
            'user_id' => $request->user()->id,
        ]);

        if ($loan) {
            $termAmount = number_format($amount / $terms, 2);
            $fraction = $amount - number_format($termAmount * $terms, 2);
            $repaymentDate = Carbon::now();
            $repayments = [];
            $counter = 1;

            while ($terms > 0) {
                $repaymentDate = $repaymentDate->copy()->addDays(env('TERM_DURATION', 7));
                $repayments[] = new Repayment([
                    'amount'         => $terms === 1 ? $termAmount + $fraction : $termAmount,
                    'repayment_date' => $repaymentDate,
                    'term'           => $counter++,
                ]);
                $terms--;
            }
            $loan->repayments()->saveMany($repayments);
        }

        $loan->repayments;

        return new JsonResponse($loan, Response::HTTP_CREATED);
    }

    /**
     * Get loans of user.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request) : JsonResponse
    {
        return new JsonResponse($this->repository->findByWith([
            ['user_id', $request->user()->id]
        ], ['repayments']));
    }

    /**
     * Get loan.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(int $id) : JsonResponse
    {
        $loan = $this->repository->find($id);

        Gate::authorize('view-loan', $loan);

        return new JsonResponse($loan);
    }

    /**
     * Approve loan.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function approve(int $id) : JsonResponse
    {
        /** @var \App\Models\Loan $loan */
        $loan = $this->repository->find($id);

        Gate::authorize('approve-loan', $loan);

        $loan->status = PaymentStatus::APPROVED;
        return new JsonResponse($loan);
    }


    /**
     * Repay loan term.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function repay(Request $request, int $id) : JsonResponse
    {
        /** @var \App\Models\Loan $loan */
        $loan = $this->repository->findByWith([
            ['id', $id],
            ['status', PaymentStatus::APPROVED]
        ], ['repayments']);

        if (!$loan) {
            throw new BadRequestException('Loan does not exist or already paid.');
        }
        
        Gate::authorize('view-loan', $loan);
        
        $repayment = $loan->repayments->where('status', PaymentStatus::PENDING->value)->first();
        
        if (!$repayment) {
            throw new BadRequestException('This term Re-Payment already done or does not exist.');
        }

        $this->validate($request, [
            'amount'  => 'required|numeric|gte:'.$repayment->amount,
        ]);

        $this->repaidTerm($loan, $repayment, $request->get('amount'));
        
        Gate::authorize('approve-loan', $loan);

        $loan->status = PaymentStatus::APPROVED;
        return new JsonResponse($loan);
    }

    protected function repaidTerm($loan, $repayment, $amount) : void
    {
        if ($amount < $repayment->amount) {
            $repayment->amount = $amount;
            $repayment->save();
        } else {

            $accessAmount = $repayment->amount - $amount;
            $repayment->amount = $amount;
            $repayment->status = PaymentStatus::PAID->value;
            $repayment->save();

            $repayment = $loan->refresh()->repayments->where('status', PaymentStatus::PENDING->value)->first();
            if (!$repayment) {
                $loan->status = PaymentStatus::PAID;
                $loan->save();
            } elseif ($accessAmount > 0) {
                $this->repaidTerm($loan, $repayment, $accessAmount);
            }
        }
    }
}
