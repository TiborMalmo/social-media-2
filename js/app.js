// prevents empty URLs after redirects on facebook. Important for the facebook-flow to work.
if (window.location.hash && window.location.hash == '#_=_') {
    window.location.hash = '';
}