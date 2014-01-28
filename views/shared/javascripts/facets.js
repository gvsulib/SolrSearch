
/* vim: set expandtab tabstop=2 shiftwidth=2 softtabstop=2 cc=80; */

/**
 * @package     omeka
 * @subpackage  solr-search
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */

jQuery(document).ready(function() {

  jQuery('.solr_facets .facet').addClass('clicker').click(function() {
    jQuery(this).toggleClass('active');
    jQuery(this).next().toggle();
    return false;
  }).next().hide();

});
