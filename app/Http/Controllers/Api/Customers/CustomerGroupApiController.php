<?php

namespace App\Http\Controllers\Api\Customers;

use App\Http\Controllers\Controller;
use App\Http\Responses\GeneralResponse\Response;
use App\Http\Responses\GeneralResponse\ResultBuilder;
use App\Repositories\Contracts\Customers\CustomerGroupRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CustomerGroupApiController extends Controller
{
    protected $groupRepo;
    protected $responseBuilder;
    protected $response;

    public function __construct(CustomerGroupRepositoryInterface $groupRepository)
    {
        $this->groupRepo = $groupRepository;
        $this->responseBuilder = new ResultBuilder;
        $this->response = new Response;
    }

    public function index(Request $request)
    {
        try {
            $query = $this->groupRepo->query();

            // Name filter
            if ($request->filled('name')) {
                $query->where('name', 'ILIKE', "%{$request->name}%");
            }

            // Code filter
            if ($request->filled('code')) {
                $query->where('code', 'ILIKE', "%{$request->code}%");
            }

            // Active filter
            if ($request->filled('is_active')) {
                $query->where('is_active', $request->is_active === '1');
            }

            $perPage = $request->get('per_page', 20);
            $groups = $query->orderBy('created_at', 'desc')->paginate($perPage)->withQueryString();
            $statistics = $this->groupRepo->getStatistics();

            $this->responseBuilder->setMessage("Customer groups retrieved successfully.");
            $this->responseBuilder->setData([
                'groups' => $groups,
                'statistics' => $statistics
            ]);
            return $this->response->generateResponse($this->responseBuilder);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage($e->getMessage())
                ->setData([]);
            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    public function show($id)
    {
        try {
            $group = $this->groupRepo->findById($id);

            if (!$group) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Customer group not found')
                    ->setData([]);
                return response()->json($this->response->generateResponse($result), 404);
            }

            $this->responseBuilder->setMessage("Customer group retrieved successfully.");
            $this->responseBuilder->setData(['group' => $group]);
            return $this->response->generateResponse($this->responseBuilder);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage($e->getMessage())
                ->setData([]);
            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:50|unique:customer_groups,code',
                'description' => 'nullable|string',
                'color' => 'nullable|string|max:20',
                'is_active' => 'boolean',
            ]);

            if ($validator->fails()) {
                $this->responseBuilder->setStatus(false);
                $this->responseBuilder->setStatusCode('422');
                $this->responseBuilder->setMessage($validator->errors()->first());
                $this->responseBuilder->setData(['errors' => $validator->errors()]);
                return response()->json($this->response->generateResponse($this->responseBuilder), 422);
            }

            DB::beginTransaction();

            $validated = $validator->validated();
            $validated['member_count'] = 0;
            $validated['is_active'] = $request->has('is_active');

            $group = $this->groupRepo->create($validated);

            DB::commit();

            $this->responseBuilder->setMessage("Customer group created successfully.");
            $this->responseBuilder->setData(['group' => $group]);
            return response()->json($this->response->generateResponse($this->responseBuilder), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage($e->getMessage())
                ->setData([]);
            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:50|unique:customer_groups,code,' . $id,
                'description' => 'nullable|string',
                'color' => 'nullable|string|max:20',
                'is_active' => 'boolean',
            ]);

            if ($validator->fails()) {
                $this->responseBuilder->setStatus(false);
                $this->responseBuilder->setStatusCode('422');
                $this->responseBuilder->setMessage($validator->errors()->first());
                $this->responseBuilder->setData(['errors' => $validator->errors()]);
                return response()->json($this->response->generateResponse($this->responseBuilder), 422);
            }

            DB::beginTransaction();

            $validated = $validator->validated();
            $validated['is_active'] = $request->has('is_active');

            $group = $this->groupRepo->update($id, $validated);

            DB::commit();

            $this->responseBuilder->setMessage("Customer group updated successfully.");
            $this->responseBuilder->setData(['group' => $group]);
            return $this->response->generateResponse($this->responseBuilder);
        } catch (\Exception $e) {
            DB::rollBack();
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage($e->getMessage())
                ->setData([]);
            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $group = $this->groupRepo->findById($id);

            if ($group && $group->member_count > 0) {
                $result = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('400')
                    ->setMessage('Cannot delete group with existing members')
                    ->setData([]);
                return response()->json($this->response->generateResponse($result), 400);
            }

            $this->groupRepo->delete($id);

            $this->responseBuilder->setMessage("Customer group deleted successfully.");
            $this->responseBuilder->setData([]);
            return $this->response->generateResponse($this->responseBuilder);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage($e->getMessage())
                ->setData([]);
            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    public function addMember(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'customer_id' => 'required|exists:customers,id',
            ]);

            if ($validator->fails()) {
                $this->responseBuilder->setStatus(false);
                $this->responseBuilder->setStatusCode('422');
                $this->responseBuilder->setMessage($validator->errors()->first());
                $this->responseBuilder->setData(['errors' => $validator->errors()]);
                return response()->json($this->response->generateResponse($this->responseBuilder), 422);
            }

            $validated = $validator->validated();
            $result = $this->groupRepo->addMember($id, $validated['customer_id']);

            if (!$result) {
                $resultBuilder = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('400')
                    ->setMessage('Customer is already a member of this group')
                    ->setData([]);
                return response()->json($this->response->generateResponse($resultBuilder), 400);
            }

            $this->responseBuilder->setMessage("Member added successfully.");
            $this->responseBuilder->setData([]);
            return $this->response->generateResponse($this->responseBuilder);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage($e->getMessage())
                ->setData([]);
            return response()->json($this->response->generateResponse($result), 500);
        }
    }

    public function removeMember(Request $request, $groupId, $customerId)
    {
        try {
            $result = $this->groupRepo->removeMember($groupId, $customerId);

            if (!$result) {
                $resultBuilder = (new ResultBuilder())
                    ->setStatus(false)
                    ->setStatusCode('404')
                    ->setMessage('Member not found in this group')
                    ->setData([]);
                return response()->json($this->response->generateResponse($resultBuilder), 404);
            }

            $this->responseBuilder->setMessage("Member removed successfully.");
            $this->responseBuilder->setData([]);
            return $this->response->generateResponse($this->responseBuilder);
        } catch (\Exception $e) {
            $result = (new ResultBuilder())
                ->setStatus(false)
                ->setStatusCode('500')
                ->setMessage($e->getMessage())
                ->setData([]);
            return response()->json($this->response->generateResponse($result), 500);
        }
    }
}
