<?php

namespace App\Http\Controllers;

use App\Forms\CustomForm;
use App\Models\Forms\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;

class FormController extends Controller
{
    /**
     * @return \Inertia\Response
     */
    public function index($slug)
    {
        $form = Form::where('slug', $slug)->firstOrFail();
        $customForm = app(CustomForm::class)->withForm($form);
        $customForm->schema = $customForm->schema();
        $customForm->setElements();

        if (!$form->is_public && !Auth::check()) {
            abort(403, 'This form is not avaiable to the public. Please login to continue.');
        }

        return Inertia::render('Forms/CustomForm', [
            'form' => $customForm,
            'title' => $form->name,
            'description' => $form->description,
            'instructions' => Str::markdown($form->instructions),
        ]);
    }
}
