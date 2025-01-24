<?php

/**
 * @package     omeka
 * @subpackage  solr-search
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */

?>


<?php queue_css_file('results'); ?>
<?php echo head(array('title' => __('Simple Search')));?>

<div class="wrapper">
  <div class="content" style="padding-bottom: 1em;">
    <div class="row-gutter">
      <div class="col-12">
        <h1 class="h2"><?php echo __('Search Results'); ?></h1>
      </div>
    </div>
  </div>
</div>


<!-- Applied facets. -->
<div class="wrapper">
  <div class="content" style="padding: 1em 3em;">
    <div class="row-gutter">
      <div id="solr-applied-facets" class="col-12">
        <h1 class="h4">Applied Limits:</h1>
          <ul class="results-applied-facets">

    <!-- Get the applied facets. -->
    <?php foreach (SolrSearch_Helpers_Facet::parseFacets() as $f): ?>
      <li class="results-applied-facets-items">

        <!-- Facet label. -->
        <?php $label = SolrSearch_Helpers_Facet::keyToLabel($f[0]); ?>
        <span class="applied-facet-label"><?php echo $label; ?></span> >
        <span class="applied-facet-value"><?php echo $f[1]; ?></span>

        <!-- Remove link. -->
        <?php $url = SolrSearch_Helpers_Facet::removeFacet($f[0], $f[1]); ?>
        (<a href="<?php echo $url; ?>">remove</a>)

      </li>
    <?php endforeach; ?>

  </ul>

      </div>
</div>
</div>
</div>


<!-- Facets. -->
<div class="wrapper">
  <div class="content">
    <div class="row-gutter">
        <div id="solr-facets" class="col-3 col-md-12">

  <h1 class="h2"><?php if ($results->response->numFound > 0) {echo __('Limit your search'); }?></h1>
<div id="solr-facets">

  <?php foreach ($results->facet_counts->facet_fields as $name => $facets): ?>

    <!-- Does the facet have any hits? -->
    <?php if (count(get_object_vars($facets))): ?>

      <!-- Facet label. -->
      <?php $label = SolrSearch_Helpers_Facet::keyToLabel($name); ?>
     <h1 class="h3"><?php echo $label; ?></h1>

      <ul>
        <!-- Facets. -->
        <?php foreach ($facets as $value => $count): ?>
          <li class="<?php echo $value; ?>">

            <!-- Facet URL. -->
            <?php $url = SolrSearch_Helpers_Facet::addFacet($name, $value); ?>

            <!-- Facet link. -->
            <a href="<?php echo $url; ?>" class="facet-value">
              <?php echo $value; ?>
            </a>

            <!-- Facet count. -->
            (<span class="facet-count"><?php echo $count; ?></span>)

          </li>
        <?php endforeach; ?>
      </ul>

    <?php endif; ?>

  <?php endforeach; ?>
</div>


<!-- Results. -->
<div id="solr-results" class="col-9 col-sm-12">

  <!-- Number found. -->
  <h2 id="num-found">
    <?php echo $results->response->numFound; ?> results
  </h2>

  <?php
  if ($results->response->numFound < 1) {
    echo "<p>No results found for your search.  Try our <a href='https://digitalcollections.library.gvsu.edu/searchtips'>Search tips</a> page for help searching.</p>";

  }
  ?>

  <?php foreach ($results->response->docs as $doc): ?>

    <!-- Document. -->
    <div class="result">

      <!-- Header. -->
      <div class="result-header">

        <!-- Record URL. -->
        <?php $url = SolrSearch_Helpers_View::getDocumentUrl($doc); ?>

        <!-- Title. -->
        <a href="<?php echo $url; ?>" class="result-title"><?php
                $title = is_array($doc->title) ? $doc->title[0] : $doc->title;
                if (empty($title)) {
                    $title = '<i>' . __('Untitled') . '</i>';
                }
                echo $title;
            ?></a>

        <!-- Result type. -->
        <!--<span class="result-type">(<?php //echo $doc->resulttype; ?>)</span>-->

      </div>
      <div class="row-gutter">
        <div class="col-4 col-md-6 col-sm-12" id="search_thumbnail">
           <?php if ($recordImage = record_image($item, 'square_thumbnail', array('alt' => $title))): ?>
      
                    <?php echo link_to($item, 'show', $recordImage, array('class' => 'result-image')); ?>
            <?php endif; ?>
        </div>
        <div class="col-8 col-md-12" id="snippets">
      <!-- Highlighting. -->
      <?php if (get_option('solr_search_hl')): ?>
      <?php $item = get_db()->getTable($doc->model)->find($doc->modelid); ?>
        <div class="snippets">
        <?php 
          $test = (array) $results->highlighting->{$doc->id};
          $numberSnippets = count($test);

          if ($numberSnippets > 0) {
            echo "<P>Your search matched in:</P>
            <ul class='hl'>";
            foreach($results->highlighting->{$doc->id} as $id => $field) {
             
              foreach($field as $hl) {
       if (strlen($hl) > 300) {
                    $match = strpos($hl, '<em>');
      $beginning = $match - 125;
      if ($beginning < 0) {$beginning = 0;}
      $hl = '...' . substr($hl, $beginning, 300) . '...';
             }
                   echo '<li class="snippet"><b>' . SolrSearch_Helpers_View::lookupElement($id) . '</b>: ' . strip_tags($hl, '<em>') . '</li>';
                  
                
              }
            }
            echo "</ul>";

          } else {
            echo metadata($item, array('Dublin Core', 'Description'), array('no_escape' => false));
                  
          }      
        ?>
                

           </div>
      </div>

       
        </div>
      <?php endif; ?>
            
    
      </div>
      <div class="clear"></div>
      <?php endforeach; ?>

  </div>
</div>
</div>
</div>
</div>



<?php echo pagination_links(); ?>
<?php echo foot();