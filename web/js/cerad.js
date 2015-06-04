(function() {
  
  // Global function
  CeradApp = function() {};

  // Ignore the warnings for now
  CeradApp.prototype.getApiLinkHref = function(links,rel) {
    var i,ii;
    for(i = 0, ii = links.length; i < ii; i++) {
      if (links[i].rel === rel) return links[i].href;
    }
    return null;
  };
  CeradApp.prototype.getJson = function(url,success,error) {
    var xhr = new XMLHttpRequest();
    xhr.responseType = 'json';
    xhr.open('GET', url, true);
    xhr.onload = function() {
      if (xhr.status === 200) {
        return success(xhr.response);
      }
      if (error) return error(xhr.status);
      alert('json error ' + url + ' ' + xhr.status);
    };
    xhr.send();
  };
})();

// Works but still not quite what I want
// var app = new CeradApp();