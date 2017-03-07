export default {isiOS, isMobile};

function isMobile() {
    return isiOS() || !Foundation.utils.is_large_up() || (Modernizr.touch && !Foundation.utils.is_xlarge_up());
}

function isiOS() {

    var iDevices = [
        'iPad Simulator',
        'iPhone Simulator',
        'iPod Simulator',
        'iPad',
        'iPhone',
        'iPod'
    ];

    if (!!navigator.platform) {
        while (iDevices.length) {
            if (navigator.platform === iDevices.pop()) {
                return true;
            }
        }
    }

    return false;
}