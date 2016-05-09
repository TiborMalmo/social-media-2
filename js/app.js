// prevents empty URLs after redirects on facebook.
if (window.location.hash && window.location.hash == '#_=_') {
    window.location.hash = '';
}