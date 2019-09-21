<?= $helper->getHeadPrintCode($entity_class_name) ?>

{% block stylesheets %}
{{ parent() }}
{% endblock %}

{% block breadcrumb %}

<section class="content-header">
    <h1>
        <?= $entity_class_name ?>
        <small>(edit)</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ path('app_dashboard') }}">Ana Sayfa</a></li>
        <li><a href="{{ path('<?= $route_name ?>_index') }}"><?= $entity_class_name ?></a></li>
        <li class="active"><a href="{{ path('<?= $route_name ?>_edit',
        {'<?= $entity_identifier ?>': <?= $entity_twig_var_singular ?>.<?= $entity_identifier ?>}) }}">
                (edit)</a></li>

    </ol>
</section>

{% endblock %}



{% block body %}
    <h1></h1>

<div class="row">
    <div class="col-md-12">
        <div class="box">
            <!--
            <div class="box-header with-border">
                <h3 class="box-title"></h3>
            </div>-->
            <!-- /.box-header -->
            <!-- form start -->

            {{ include('<?= $route_name ?>/_form.html.twig') }}

            <div class="box-footer">
                <a class="btn btn-primary" style="margin-right: 10px;"
                   href="{{ path('<?= $route_name ?>_show', {'<?= $entity_identifier ?>': <?= $entity_twig_var_singular ?>.<?= $entity_identifier ?>}) }}">Geri</a>
                <button class="btn btn-success">{{ button_label|default('Güncelle') }}</button>

            </div>

            {{ form_end(form) }}

        </div>
    </div>
</div>
{% endblock %}

{% block javascripts %}
{{ parent() }}
{% endblock %}