(function(){
    // Global configuration for frontend -> backend connection
    window.LB_CONFIG = window.LB_CONFIG || {};
    // Defaults (override before loading if needed)
    // In development (localhost) use explicit ports; in production use relative paths
    const isLocal = (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1');
    window.LB_CONFIG.API_BASE = window.LB_CONFIG.API_BASE || (isLocal ? 'http://localhost:8080/backend' : '/backend');
    window.LB_CONFIG.WS_BASE = window.LB_CONFIG.WS_BASE || (isLocal ? 'ws://localhost:5000' : ((window.location.protocol === 'https:') ? 'wss://' : 'ws://') + window.location.host);

    // Wrap fetch to rewrite backend-relative paths to API_BASE
    // NOTE: only rewrites string URLs. Request objects (streams) are not modified.
    const _fetch = window.fetch.bind(window);
    window.fetch = function(input, init){
        try{
            if (typeof input === 'string'){
                // Match patterns like '../../backend/..', 'backend/..', '/backend/..'
                let m = input.match(/^(?:\.\.\/)*(?:backend\/)(.*)$/);
                if (m && m[1] !== undefined){
                    input = window.LB_CONFIG.API_BASE.replace(/\/$/, '') + '/' + m[1];
                } else if (input.startsWith('/backend/')){
                    input = window.LB_CONFIG.API_BASE.replace(/\/$/, '') + '/' + input.replace(/^\/backend\//, '');
                } else if (input.startsWith('backend/')){
                    input = window.LB_CONFIG.API_BASE.replace(/\/$/, '') + '/' + input.replace(/^backend\//, '');
                }
            }
        }catch(e){
            // fallback: proceed with original
        }
        return _fetch(input, init);
    };

    // Wrap WebSocket to rewrite localhost:5000 to WS_BASE
    const _WebSocket = window.WebSocket;
    function WrappedWebSocket(url, protocols){
        try{
            if (typeof url === 'string'){
                // Parse absolute or relative URL
                try{
                    const parsed = new URL(url, window.location.href);
                    if ((parsed.hostname === 'localhost' || parsed.hostname === '127.0.0.1') && parsed.port === '5000'){
                        const path = parsed.pathname + parsed.search;
                        url = window.LB_CONFIG.WS_BASE.replace(/\/$/, '') + path;
                    }
                }catch(e){
                    // If URL parsing fails, handle simple relative socket paths
                    if (url.startsWith('/socket.io') || url.startsWith('socket.io')){
                        const path = url.replace(/^\/?/, '/');
                        url = window.LB_CONFIG.WS_BASE.replace(/\/$/, '') + path;
                    }
                }
            }
        }catch(e){ }
        return new _WebSocket(url, protocols);
    }
    WrappedWebSocket.prototype = _WebSocket.prototype;
    window.WebSocket = WrappedWebSocket;

})();
