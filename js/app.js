// prevents empty URLs after redirects.
if (window.location.hash && window.location.hash == '#_=_') {
    window.location.hash = '';
}