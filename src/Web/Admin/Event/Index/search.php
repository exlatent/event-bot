<?php

use App\Domain\Event\Event;
use App\Web\Admin\Event\Index\SearchForm;
use Yiisoft\Html\Html;

/** @var SearchForm $filter */
var_dump((bool)$filter->hasDuplicates);
?>


<div class="search">
    <?= Html::form()
        ->get()
        ->action('/admin/event')
        ->open()
    ?>

    <label for="query" class="form-label">Query</label>
    <?= Html::textInput('query', $filter->query, [
        'class' => 'form-control',
    ]) ?>

    <label for="dateFrom" class="form-label">Date From</label>
    <?= Html::textInput('dateFrom', $filter->dateFrom, [
        'type'  => 'datetime-local',
        'class' => 'form-control',
    ]) ?>

    <label for="dateTo" class="form-label">Date To</label>
    <?= Html::textInput('dateTo', $filter->dateTo, [
        'type'  => 'datetime-local',
        'class' => 'form-control',
    ]) ?>

    <label for="state" class="form-label">State</label>
    <select name="state" class="form-control">
        <option value=""> -</option>
        <?php foreach (Event::$states as $index => $state) : ?>
            <?= Html::option($state, $index)->addAttributes([
                'selected' => (string) $index === $filter->state,
            ]) ?>
        <?php endforeach; ?>
    </select>

    <label for="state" class="form-label">Has duplicates</label>
    <select name="hasDuplicates" class="form-control">
        <option value="" <?= is_null($filter->hasDuplicates) ? 'selected' : ''?>> -</option>
        <option value="1" <?= (bool)$filter->hasDuplicates ? 'selected' : '' ?>>Yes</option>
        <option value="0"<?= !(bool)$filter->hasDuplicates ? 'selected' : '' ?>>No</option>
    </select>


    <?= Html::submitButton('Search') ?>

    <?= Html::a('Clear', '/admin/event', ['class' => 'btn btn-link']) ?>

    <?= Html::form()->close() ?>
</div>


