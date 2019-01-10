<?php

/*
 * @copyright   2018 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Filter\Segment;


use Mautic\LeadBundle\Segment\ContactSegmentFilter;
use Mautic\LeadBundle\Segment\ContactSegmentFilterCrate;
use Mautic\LeadBundle\Segment\Query\Filter\FilterQueryBuilderInterface;
use Mautic\LeadBundle\Segment\Query\QueryBuilder;
use Mautic\LeadBundle\Segment\TableSchemaColumnsCache;
use MauticPlugin\MauticRecommenderBundle\Filter\Segment\Decorator\Decorator;
use MauticPlugin\MauticRecommenderBundle\Filter\Segment\EventListener\Choices;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FilterFactory
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var TableSchemaColumnsCache
     */
    private $schemaCache;


    /**
     * SegmentFilterFactory constructor.
     *
     * @param ContainerInterface      $container
     * @param TableSchemaColumnsCache $schemaCache
     * @param Choices                 $segmentChoices
     */
    public function __construct(ContainerInterface $container, TableSchemaColumnsCache $schemaCache)
    {

        $this->container = $container;
        $this->schemaCache = $schemaCache;
    }

    /**
     * @param $filter
     *
     * @return ContactSegmentFilter
     */
    public function getContactSegmentFilter($filter, $decorator)
    {
        $contactSegmentFilterCrate = new ContactSegmentFilterCrate($filter);
        if ($contactSegmentFilterCrate->isDateType()) {
            $decorator = $this->container->get(
                'mautic.lead.model.lead_segment.decorator.date.optionFactory'
            )->getDateOption($contactSegmentFilterCrate);
        }
            $filterQueryBuilder = $this->container->get($decorator->getQueryType($contactSegmentFilterCrate));
        return  new ContactSegmentFilter($contactSegmentFilterCrate, $decorator, $this->schemaCache, $filterQueryBuilder);
    }

    /**
     * @param              $filter
     * @param QueryBuilder $qb
     */
    public function applySegmentQuery($filter, QueryBuilder $qb)
    {
        if (isset($filter['crate']) && isset($filter['filter']) && $filter['filter'] instanceof ContactSegmentFilter) {
            /** @var FilterQueryBuilderInterface $filterQueryBuilder */
            $filterQueryBuilder = $this->container->get($this->decorator->getQueryType($filter['crate']));
            $filterQueryBuilder->applyQuery($qb, $filter['filter']);
        }
    }
}
