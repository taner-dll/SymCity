<?= $helper->getHeadPrintCode($entity_class_name) ?>

{% block stylesheets %}
{{ parent() }}
{% endblock %}

{% block breadcrumb %}

<section class="content-header">
    <h1>
        <?= $entity_class_name ?>
        <small></small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ path('app_dashboard') }}">Ana Sayfa</a></li>
        <li><a href="{{ path('<?= $route_name ?>_index') }}"><?= $entity_class_name ?></a></li>
        <li class="active"><a href="{{ path('<?= $route_name ?>_show',
        {'<?= $entity_identifier ?>': <?= $entity_twig_var_singular ?>.<?= $entity_identifier ?>}) }}"></a></li>

    </ol>
</section>

{% endblock %}


{% block body %}

<div class="box">

    <!--
    <div class="box-header with-border">
        <h3 class="box-title">Gezilecek Yerler</h3>
    </div>

   -->
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">
                <table class="table table-bordered">
                    <tbody>
                    <?php foreach ($entity_fields as $field): ?>
                        <tr>
                            <th><?= ucfirst($field['fieldName']) ?></th>
                            <td>{{ <?= $helper->getEntityFieldPrintCode($entity_twig_var_singular, $field) ?> }}</td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </div>

    </div>
    <!-- /.box-body -->
    <div class="box-footer">
        <a href="{{ path('<?= $route_name ?>_index') }}" class="btn btn-primary" style="margin-right: 10px;">Liste</a>
        <a href="{{ path('<?= $route_name ?>_edit', {'<?= $entity_identifier ?>': <?= $entity_twig_var_singular ?>.<?= $entity_identifier ?>}) }}"
           class="btn btn-warning"
           style="margin-right: 10px;">Düzenle</a>
        <!-- Button trigger modal -->
        <a class="btn btn-danger" data-toggle="modal" data-target="#myModal">
            Sil
        </a>
        {{ include('<?= $route_name ?>/_delete_form.html.twig') }}
    </div>
    <!-- /.box-footer-->
</div>



{% for message in app.flashes('success') %}
<input type="hidden" id="success_message" value="{{ message }}"/>
{% endfor %}


{% endblock %}

{% block javascripts %}
{{ parent() }}
{{ encore_entry_script_tags('business') }}
{% endblock %}