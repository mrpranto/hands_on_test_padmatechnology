<?php


namespace App\Services;


use App\Models\Contact;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ContactServices extends BaseServices
{
    public function __construct(Contact $contact)
    {
        $this->model = $contact;
    }

    public function contacts($request)
    {
        return $this->model->newQuery()
            ->with('contactDetails', 'groups')
            ->when($request->email, function (Builder $builder) use ($request) {
                $builder->whereHas('contactDetails', function (Builder $builder) use ($request) {
                    $builder->where('email', $request->email);
                });
            })
            ->when($request->phone, function (Builder $builder) use ($request) {
                $builder->whereHas('contactDetails', function (Builder $builder) use ($request) {
                    $builder->where('phone', $request->phone);
                });
            })
            ->when($request->name, function (Builder $builder) use ($request) {
                $builder->where('name', 'like', "%{$request->name}%");
            })
            ->when($request->group, function (Builder $builder) use ($request) {
                $builder->whereHas('groups', function (Builder $builder) use ($request) {
                    $builder->where('group_id', $request->group);
                });
            })
            ->get();
    }

    public function validate($request)
    {
        $request->validate([

            'name' => 'required|string',
            'email.*' => 'required|unique:contact_details,email',
            'phone.*' => 'required|unique:contact_details,phone',
            'group_id.*' => 'required',
            'is_favorit' => 'nullable|numeric'

        ]);

        return $this;
    }

    public function store($request)
    {
        DB::transaction(function () use ($request) {
            $this->storeContact($request)
                ->storeContactDetails($request)
                ->syncGroup($request->group_id);
        });
    }

    public function storeContact($request): ContactServices
    {
        $this->model = $this->model
            ->newQuery()
            ->create([
                'name' => $request->name,
                'is_favorite' => $request->is_favorit,
            ]);

        return $this;
    }

    public function storeContactDetails($request): ContactServices
    {
        if (is_array($request->email)) {
            $contactDetails = [];
            foreach ($request->email as $key => $email) {
                $contactDetails[] = [
                    'contact_id' => $this->model->id,
                    'email' => $email,
                    'phone' => $request->phone[$key]
                ];
            }

            $this->model
                ->contactDetails()
                ->insert($contactDetails);
        }

        return $this;
    }

    public function syncGroup($group_id)
    {
        $this->model
            ->groups()
            ->sync($group_id);
    }


    public function update($request)
    {
        DB::transaction(function () use ($request){

            $this->updateContact($request)
                ->updateContactDetails($request)
                ->syncGroup($request->group_id);

        });
    }

    public function updateContact($request)
    {
        $this->model->update([
            'name' => $request->name,
            'is_favorite' => $request->is_favorit,
        ]);

        return $this;
    }

    public function updateContactDetails($request): ContactServices
    {
        if (is_array($request->email)) {
            $contactDetails = [];
            foreach ($request->email as $key => $email) {
                if (array_key_exists($key,$request->contact_details_id) && $request->contact_details_id[$key] != null){
                    $this->model
                        ->contactDetails()
                        ->where('id', $request->contact_details_id[$key])
                        ->update([
                            'email' => $email,
                            'phone' => $request->phone[$key]
                        ]);
                }else{
                    $contactDetails[] = [
                        'contact_id' => $this->model->id,
                        'email' => $email,
                        'phone' => $request->phone[$key]
                    ];
                }
            }
            $this->model
                ->contactDetails()
                ->insert($contactDetails);
        }

        return $this;
    }

    public function deleteContact(): bool
    {
        $this->model->groups()->detach();
        $this->model->contactDetails()->delete();
        $this->model->delete();

        return true;
    }
}
