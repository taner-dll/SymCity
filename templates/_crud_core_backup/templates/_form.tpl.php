{% form_theme form 'form/fields.html.twig' %}
<div class="box-body">
    {{ form_start(form) }}
    <div class="col-md-6">
        <div class="form-group">
            {{ form_widget(form) }}
        </div>
    </div>
</div>