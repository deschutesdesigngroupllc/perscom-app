<?php

namespace App\Http\Controllers;

use App\Forms\CustomForm;
use App\Models\Forms\Form;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Laraform\Traits\ProcessesForm;

class FormController extends Controller
{
    use ProcessesForm;

    /**
     * @return \Inertia\Response
     */
    public function index($slug)
    {
        $form = Form::where('slug', $slug)->firstOrFail();
        $customForm = app(CustomForm::class);
        $customForm->schema = $customForm->schema($form->fields->sortBy('pivot.order')->all(), $form->id);
        $customForm->setElements();

        if (! $form->is_public && ! Gate::check('view', $form)) {
            abort(403, 'You do not have permission to view this form.');
        }

        return Inertia::render('Forms/FormWrapper', [
            'form' => $customForm,
            'title' => $form->name,
            'description' => $form->description,
            'instructions' => $form->instructions ? Str::markdown($form->instructions) : null,
            'success' => $form->success_message ?? 'The form has been successfully submitted.',
        ]);
    }
}
