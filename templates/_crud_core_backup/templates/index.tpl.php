<?= $helper->getHeadPrintCode($entity_class_name); ?>

{% block stylesheets %}
{{ parent() }}
{% endblock %}

{% block breadcrumb %}

<section class="content-header">
    <h1>
        <?= $entity_class_name ?>
        <small>Liste</small>
    </h1>

    <ol class="breadcrumb">
        <li><a href="{{ path('app_dashboard') }}">Ana Sayfa</a></li>
        <li class="active"><a href="{{ path('<?= $route_name ?>_index') }}"><?= $entity_class_name ?></a></li>

    </ol>
</section>

{% endblock %}


{% block body %}
<div class="row">
    <div class="col-md-12">

        <div class="box">
            <!--
            <div class="box-header">
                <h3 class="box-title"></h3>
            </div>
            -->
            <!-- /.box-header -->
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div id="example1_wrapper"
                             class="dataTables_wrapper form-inline dt-bootstrap table-responsive">
                            <table id="example1" class="table table-bordered table-striped
                                table-hover table-condensed
                                dataTable" role="grid" aria-describedby="example1_info">
                                <thead>
                                <tr role="row">
                                    <?php foreach ($entity_fields as $field): ?>
                                        <th><?= ucfirst($field['fieldName']) ?></th>
                                    <?php endforeach; ?>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                {% for <?= $entity_twig_var_singular ?> in <?= $entity_twig_var_plural ?> %}
                                <tr>
                                    <?php foreach ($entity_fields as $field): ?>
                                        <td>{{ <?= $helper->getEntityFieldPrintCode($entity_twig_var_singular, $field) ?> }}</td>
                                    <?php endforeach; ?>
                                    <td>
                                        <a href="{{ path('<?= $route_name ?>_show', {'<?= $entity_identifier ?>': <?= $entity_twig_var_singular ?>.<?= $entity_identifier ?>}) }}"
                                           class="btn btn-xs btn-primary pull-left">Göster</a>
                                    </td>
                                </tr>
                                {% else %}
                                <tr>
                                    <td colspan="<?= (count($entity_fields) + 1) ?>">no records found</td>
                                </tr>
                                {% endfor %}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.box-body -->
        </div>
    </div>
</div>

{% for message in app.flashes('success') %}
<input type="hidden" id="success_message" value="{{ message }}"/>
{% endfor %}

{% endblock %}

{% block javascripts %}
{{ parent() }}
{% endblock %}