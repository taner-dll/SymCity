<?= $helper->getHeadPrintCode($entity_class_name.' - Yeni Ekle') ?>

{% block stylesheets %}
    {{ parent() }}
{% endblock %}

{% block breadcrumb %}

<section class="content-header">
    <h1>
        <?= $entity_class_name ?>
        <small>Yeni Ekle</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ path('app_dashboard') }}">Ana Sayfa</a></li>
        <li><a href="{{ path('<?= $route_name ?>_index') }}"><?= $entity_class_name ?></a></li>
        <li class="active"><a href="{{ path('<?= $route_name ?>_new') }}">
                Yeni Ekle</a></li>
    </ol>
</section>

{% endblock %}

{% block body %}

<div class="row">
    <div class="col-md-12">
        <div class="box">
            <!--
            <div class="box-header with-border">
                <h3 class="box-title">Gezilecek Yerler - Yeni Ekle</h3>
            </div>-->
            <!-- /.box-header -->
            <!-- form start -->

            {{ include('<?= $route_name ?>/_form.html.twig') }}

            <div class="box-footer">
                <a class="btn btn-primary" style="margin-right: 10px;" href="{{ path('<?= $route_name ?>_index') }}">
                    Liste</a>
                <button class="btn btn-success">{{ button_label|default('Kaydet') }}</button>

            </div>

            {{ form_end(form) }}

        </div>
    </div>
</div>
{% endblock %}

{% block javascripts %}
{{ parent() }}
{% endblock %}
