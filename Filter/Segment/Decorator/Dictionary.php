<?php

/*
 * @copyright   2018 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Filter\Segment\Decorator;


use Mautic\LeadBundle\Segment\Query\Filter\ForeignValueFilterQueryBuilder;
use MauticPlugin\MauticRecommenderBundle\Filter\Fields\Fields;
use MauticPlugin\MauticRecommenderBundle\Filter\Query\BaseFilterQueryBuilder;
use MauticPlugin\MauticRecommenderBundle\Filter\Segment\Query\SegmentEventQueryBuilder;
use MauticPlugin\MauticRecommenderBundle\Filter\Segment\Query\SegmentEventValueQueryBuilder;

class Dictionary
{
    CONST ALLOWED_TABLES = ['recommender_event_log', 'recommender_event_log_property_value', 'recommender_item_property_value'];

    /**
     * @var Fields
     */
    private $fields;


    /**
     * SegmentChoices constructor.
     *
     * @param Fields              $fields
     */
    public function __construct(Fields $fields)
    {

        $this->fields = $fields;
    }

    public function getDictionary()
    {
        $dictionary = [];
        foreach (self::ALLOWED_TABLES as $table) {
            $fields = $this->fields->getFields($table);
            foreach ($fields as $key => $field) {

                switch ($table) {
                    case 'recommender_item':
                        $dictionary[$key] = [
                            'type'          => BaseFilterQueryBuilder::getServiceId(),
                            'foreign_table' => $table,
                            'field'         => $key,
                        ];
                        break;
                    case 'recommender_item_property_value':
                        $dictionary[$key] = [
                            'type'          => ForeignValueFilterQueryBuilder::getServiceId(),
                            'foreign_table' => $table,
                            'field'         => $key,
                        ];
                        break;
                    case 'recommender_event_log':
                        $dictionary[$key] = [
                            'type'          => SegmentEventQueryBuilder::getServiceId(),
                            'foreign_table' => $table,
                            'foreign_table_field' => 'event_log_id',
                            'table_field'         => 'event_log_id',
                            'field'       => $key,
                        ];
                        break;
                    case 'recommender_event_log_property_value':
                        $dictionary[$key] = [
                            'type'          => SegmentEventValueQueryBuilder::getServiceId(),
                            'foreign_table' => $table,
                            'foreign_table_field' => 'event_log_id',
                            'table_field'         => 'event_log_id',
                            'field'       => $key,
                        ];
                        break;
                }
            }
        }
        return $dictionary;
    }
}
