$( document ).ready(function() {
  var userSearch = (function() {

    var searchForm = $('#searchForm');

    var executeSearch = function(e) {
      e.preventDefault();
      $('#waiting').remove();
      $('#submitSearch').prop("disabled", true);
      searchTerms = $('#search').val();
      $('<div id="waiting"><img src="/images/icons/ajax-loader.gif" /> Searching...</div>').insertBefore( "#search-results");
      $('#search-results').load("/admin/user-search?search=" + encodeURIComponent(searchTerms) + " #searchResults");
      $('#waiting').remove();
      $('#submitSearch').prop("disabled", false);
    }

    var bindFunctions = function() {
      $("#submitSearch").on("click", executeSearch);
    };

    var addSearchButton = function() {
      $('<button id="submitSearch" type="submit" class="btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i> Find User</button>').insertAfter('#searchDiv');
    };

    var init = function() {
      addSearchButton();
      bindFunctions();
    };

    return {
      init: init
    };

  })();

  userSearch.init();
});
