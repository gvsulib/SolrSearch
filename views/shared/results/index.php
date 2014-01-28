<?php

/* vim: set expandtab tabstop=2 shiftwidth=2 softtabstop=2 cc=80; */

/**
 * @package     omeka
 * @subpackage  solr-search
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */

?>

<?php
  $pageTitle = __('Browse Items'); //TODO: Should this be browse items?
  echo head(array('title' => $pageTitle, 'id' => 'items', 'bodyclass' => 'browse'));
?>

<div id="primary" class="solr_results results">
  <h1><?php echo $pageTitle; ?></h1>

  <div id="solr_results" class="item-list">
    <div id="solr_search" class="search solr_remove_facets">
      <?php //TODO: Fix button on this... ?>
      <?php echo SolrSearch_Helpers_View::createSearchForm(); ?>
    </div>
    <div id="appliedParams">
      <h3>You searched for:</h3>
      <?php echo SolrSearch_Helpers_Query::removeFacets(); ?>
    </div>

    <?php echo pagination_links(array('partial_file' => 'common/pagination.php')); ?>

    <?php if(!empty($facets)): ?>
      <?php $query = SolrSearch_Helpers_Query::getParams(); ?>
      <div class="solr_facets_container">
        <h3>Limit your search</h3>
        <div class="solr_facets">
        <?php foreach ((array)$results->facet_counts->facet_fields as $facet => $values): ?>
            <?php $props = get_object_vars($values); ?>
            <?php if (!empty($props)): ?>
                <h4 class="facet"><?php echo SolrSearch_Helpers_Query::parseFacet($facet); ?></h4>
                <ul class="facet-list">
                    <?php foreach($values as $label => $count): ?>
                        <li><?php echo SolrSearch_Helpers_Query::createFacetHtml($query, $facet, $label, $count); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

    <div id="results">
      <h2 class="results">
        <?php echo __('%s results', $results->response->numFound); ?>
      </h2>
    <?php foreach($results->response->docs as $doc): ?>
    <div class="item" id="solr_<?php echo $doc->__get('id'); ?>">
      <div class="details">
        <div class="title">
          <h2><?php echo SolrSearch_Helpers_View::createResultLink($doc); ?></h2>
        </div>

        <div class='resultbody'>
          <?php $image = $doc->__get('image');?>
          <?php if($image): ?>
          <div class="image">
            <?php echo SolrSearch_Helpers_View::createResultImgHtml($image, SolrSearch_Helpers_View::getDocTitle($doc)); ?>
          </div>
          <?php endif; ?>

          <div class='textfields'>
            <?php if($results->responseHeader->params->hl == true): ?>
            <div class="solr_highlight">
              <?php echo SolrSearch_Helpers_View::displaySnippets($doc->id, $results->highlighting); ?>
            </div>
            <?php endif; ?>

            <?php $tags = $doc->__get('tag'); ?>
            <?php if($tags): ?>
              <div class="tags">
                <strong>Tags:</strong>
                <?php echo SolrSearch_Helpers_View::tagsToStrings($tags); ?>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
    </div>
  </div>
</div>
<?php
  echo foot();
