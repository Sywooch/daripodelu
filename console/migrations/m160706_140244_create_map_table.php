<?php

use yii\db\Migration;

/**
 * Handles the creation for table `map_table`.
 */
class m160706_140244_create_map_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%map}}', [

            'id' => $this->primaryKey(),

            'vendor' => $this->string(30)->notNull()->comment('Карта'),

            'name' => $this->string(255)->notNull()->comment('Название карты'),

            'module_id' => $this->string(40)->defaultValue(NULL)->comment('Модуль'),

            'controller_id' => $this->string(40)->notNull()->comment('Контроллер'),

            'action_id' => $this->string(40)->notNull()->comment('Действие'),

            'ctg_id' => $this->integer()->unsigned()->defaultValue(NULL)->comment('Категория'),

            'item_id' => $this->integer()->unsigned()->defaultValue(NULL)->comment('Элемент'),

            'center_lat' => $this->decimal(14, 10)->notNull()->comment('Широта центра карты'),

            'center_lng' => $this->decimal(14, 10)->notNull()->comment('Долгота центра карты'),

            'point_lat' => $this->decimal(14, 10)->notNull()->comment('Широта объекта'),

            'point_lng' => $this->decimal(14, 10)->notNull()->comment('Долгота объекта'),

            'point_label' => $this->string(255)->defaultValue(NULL)->comment('Текст метки'),

            'zoom' => $this->smallInteger()->unsigned()->defaultValue(10)->comment('Масштаб'),

            'type' => $this->string(40)->notNull()->comment('Тип карты'),

            'geolocation_control' => $this->smallInteger(1)->unsigned()->defaultValue(0)->comment('Кнопка "Геолокация"'),

            'search_control' => $this->smallInteger(1)->unsigned()->defaultValue(0)->comment('Поисковая строка'),

            'route_editor' => $this->smallInteger(1)->unsigned()->defaultValue(0)->comment('Кнопка "Построение маршрута"'),

            'traffic_control' => $this->smallInteger(1)->unsigned()->defaultValue(0)->comment('Кнопка "Пробки"'),

            'type_selector' => $this->smallInteger(1)->unsigned()->defaultValue(1)->comment('Кнопка "Выбор типа карты"'),

            'fullscreen_control' => $this->smallInteger(1)->unsigned()->defaultValue(0)->comment('Кнопка "Полноэкранный режим"'),

            'zoom_control' => $this->smallInteger(1)->unsigned()->defaultValue(1)->comment('Элемент управления "Изменение масштаба"'),

            'ruler_control' => $this->smallInteger(1)->unsigned()->defaultValue(1)->comment('Элемент управления "Измерерние расстояния"'),

            'status' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(1)->comment('Статус'),

        ], 'ENGINE = InnoDB');

        $this->createIndex(
            'idx-map-type',
            '{{%map}}',
            'type'
        );

        $this->createIndex(
            'idx-map-status',
            '{{%map}}',
            'status'
        );

        $this->createIndex(
            'idx-map-mcaci',
            '{{%map}}',
            ['module_id', 'controller_id', 'action_id', 'ctg_id', 'item_id'],
            true
        );

        $this->createIndex(
            'idx-map-mca',
            '{{%map}}',
            ['module_id', 'controller_id', 'action_id']
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropIndex(
            'idx-map-mca',
            '{{%map}}'
        );

        $this->dropIndex(
            'idx-map-mcaci',
            '{{%map}}'
        );

        $this->dropIndex(
            'idx-map-status',
            '{{%map}}'
        );

        $this->dropIndex(
            'idx-map-type',
            '{{%map}}'
        );

        $this->dropTable('{{%map}}');
    }
}
