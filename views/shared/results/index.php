<?php

/**
 * @package     omeka
 * @subpackage  solr-search
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */

?>


<!--?php queue_css_file('results'); ?-->
<?php echo head(array('title' => __('Simple Search')));?>

<div class="wrapper">
 <div class="content" style="padding-top: 0; padding-bottom: 0;">
    <div class="row">
      <div class="col-12">
<h1 class="h2"><?php echo __('Search Results'); ?></h1>
</div>
</div>
</div>
</div>
<div class="wrapper-full-width wrapper-background wrapper-light">
  <div style="background-color: #f7f7f7;">
  <div class="content" style="padding-top: 0; padding-bottom: 0;">

    <div class="row">
      <div class="col-12">
  <p style="margin-top: 1em;">
    <?php echo $results->response->numFound; ?> Results for &#8220;<?php echo array_key_exists('q', $_GET) ? $_GET['q'] : ''; ?>&#8221;
</p>
      </div>
    </div>
  </div>
</div>
</div>


<div class="wrapper-full-width wrapper-background wrapper-light">
<div style="background-color: #f7f7f7;">
<div class="row content" style="padding: 0 3em;">


<!-- Facets. -->
<div id="solr-facets" class="col-3 col-md-6 col-sm-12">

  <h1 class="h3"><?php if ($results->response->numFound > 0) {echo __('Limit your search'); }?></h1>
 <?php if(count(SolrSearch_Helpers_Facet::parsefacets())) : ?>
  <!-- Applied facets. -->
<div id="solr-applied-facets">

<p>Applied Limits:</p>
  <ul class="results-applied-facets">

    <!-- Get the applied facets. -->
    <?php foreach (SolrSearch_Helpers_Facet::parseFacets() as $f): ?>
      <li class="results-applied-facets-items">

        <!-- Facet label. -->
        <?php $label = SolrSearch_Helpers_Facet::keyToLabel($f[0]); ?>
        <span class="applied-facet-label"><?php echo $label; ?> : <?php echo $f[1]; ?></span> >

        <!-- Remove link. -->
        <?php $url = SolrSearch_Helpers_Facet::removeFacet($f[0], $f[1]); ?>
        <a href="<?php echo $url; ?>" aria-label="remove">X</a>
      </li>
    <?php endforeach; ?>

  </ul>

</div>
 <?php endif; ?>

  <?php foreach ($results->facet_counts->facet_fields as $name => $facets): ?>

    <!-- Does the facet have any hits? -->
    <?php if (count(get_object_vars($facets))): ?>

      <!-- Facet label. -->
      <?php $label = SolrSearch_Helpers_Facet::keyToLabel($name); ?>
      <strong><?php echo $label; ?></strong> (<?php echo (count(get_object_vars($facets))); ?>)

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
<div id="solr-results" class="col-9 col-m-6 col-sm-12" style="background-color: #ffffff; padding-left: 1.5em;">

  <?php
  if ($results->response->numFound < 1) {
    echo "<p>No results found for your search.  Try our <a href='https://digitalcollections.library.gvsu.edu/searchtips'>Search tips</a> page for help searching.</p>";

  }
  ?>

  <?php foreach ($results->response->docs as $doc): ?>

    <!-- Document. -->
    <div class="result">

    <div class="row content">
       <?php $item = get_db()->getTable($doc->model)->find($doc->modelid); ?>

      <!-- Header. -->
      <div class="result-header col-12">

        <!-- Record URL. -->
        <?php $url = SolrSearch_Helpers_View::getDocumentUrl($doc); ?>

        <!-- Title. -->
        <h1 class="h2"><a href="<?php echo $url; ?>" class="result-title"><?php
                $title = is_array($doc->title) ? $doc->title[0] : $doc->title;
                if (empty($title)) {
                    $title = '<em>' . __('Untitled') . '</em>';
                }
                echo $title;
            ?></a></h1>

        <!-- Result type. -->
        <!--<span class="result-type">(<?php //echo $doc->resulttype; ?>)</span>-->

      </div>
    </div>
      <div class="row content">
        <div class="col-3 col-md-6 col-sm-12" id="search_thumbnail">
            <?php if ($recordImage = record_image($item, 'square_thumbnail', array('alt' => $title))): ?>
      
                    <?php echo link_to($item, 'show', $recordImage, array('class' => 'result-image')); ?>
            <?php endif; ?>
          </div>
          <div class="col-9 col-md-6 col-sm-12">
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
<style>
li.results-applied-facets-items {
  display: flex;
  padding: .5em;
  background-color: #d9d9d9;
  font-weight: bold;
  margin-bottom: 1em;
}
.results-applied-facets-items span {
  display: block;
  width: 88%;
 
}
.results-applied-facets-items a {
  display: block;
    width: 8%;
    color: #fff;
    text-decoration: none;
    font-size: .8em;
    background-color: #333;
    text-align: center;
    border-radius: 50%;
    height: 1.8em;
}
ul.results-applied-facets {
  list-style: none;
  margin-left: 0;
}
</style>



<?php echo pagination_links(); ?>
<?php echo foot();
