(function($){
    $(document).ready(function() {
        $.ajax({
            url: '/backstage/country-statistic',
            type: 'get',
            success: function (data) {
                //WORLD MAP
                $('#world-map').vectorMap({
                    map: 'world_merc_en',
                    backgroundColor: '#ffffff',
                    zoomOnScroll: false,
                    regionStyle: {
                        initial: {
                            fill: '#e1e1e1',
                            stroke: 'none',
                            "stroke-width": 0,
                            "stroke-opacity": 1
                        },
                        hover: {
                            "fill-opacity": 0.8
                        },
                        selected: {
                            fill: '#8dc859'
                        },
                        selectedHover: {
                        }
                    },
                    series: {
                        regions: [{
                            values: data,
                            scale: ['#6fc4fe', '#2980b9'],
                            normalizeFunction: 'polynomial'
                        }]
                    },
                    onRegionLabelShow: function(e, el, code){
                        if(typeof data[code] !== 'undefined'){
                            el.html(el.html()+' ('+data[code]+')');
                        }

                    }
                });
            }
        });

        $.ajax({
            url: '/backstage/gender-statistic',
            type: 'get',
            success: function (data) {
                Morris.Donut({
                    element: 'hero-donut',
                    data: data,
                    colors: ['#6fc4fe', '#f1c40f', '#e1e1e1'],
                    formatter: function (y) { return y + "%" },
                    resize: true
                });
            }
        });

        $.ajax({
            url: '/backstage/age-statistic',
            type: 'get',
            success: function (data) {
                Morris.Bar({
                    element: 'hero-bar',
                    data: data,
                    xkey: 'label',
                    ykeys: ['value'],
                    labels: $.map(data, function(o){
                        return o.label;
                    }),
                    hoverCallback: function (index, options, content, row) {
                        return  row.value + "%";
                    },
                    barRatio: 0.4,
                    xLabelAngle: 35,
                    hideHover: 'auto',
                    resize: true
                });
            }
        });

        var placementRight = 'right';
        var placementLeft = 'left';

        if ($('body').hasClass('rtl')) {
            placementRight = 'left';
            placementLeft = 'right';
        }

        // Define the tour!
        var tour = {
            id: "centaurus-intro",
            steps: [
                {
                    target: "logout",
                    title: "Logout",
                    content: "Click here to logout!",
                    placement: placementLeft,
                    yOffset: 10,
                    zindex: 999
                },
                {
                    target: "active-users",
                    title: "Active Users",
                    content: "Go to menu 'Users' then 'Members' to see all users",
                    placement: placementRight,
                    yOffset: 10
                },
                {
                    target: "active-posts",
                    title: "Active Posts",
                    content: "Go to menu 'Posts' to see all posts",
                    placement: placementRight,
                    yOffset: 10
                },
                {
                    target: "pending-reports",
                    title: "Pending Reports",
                    content: "Go to menu 'Reports' to view all reports",
                    placement: placementRight,
                    yOffset: 10
                },
                {
                    target: "active-hot-posts",
                    title: "Active Hot Posts",
                    content: "Go to menu 'Posts' to see all posts",
                    placement: placementLeft,
                    yOffset: 10
                },
                {
                    target: "hero-bar",
                    title: "Age Statistics",
                    content: "Here are users age statistics",
                    placement: 'top',
                    yOffset: 10
                },
                {
                    target: "hero-donut",
                    title: "Gender Statistics",
                    content: "Here are users gender statistics",
                    placement: 'top',
                    yOffset: 10
                },
                {
                    target: "world-map",
                    title: "Country Statistics",
                    content: "Here are users country statistics",
                    placement: 'top',
                    yOffset: 10
                },
                {
                    target: "latest-users",
                    title: "Latest Users",
                    placement: 'bottom',
                    yOffset: 10
                },
                {
                    target: "latest-posts",
                    title: "Latest Posts",
                    placement: 'bottom',
                    yOffset: 10
                }

            ],
            showPrevButton: true
        };

        // Start the tour!
        //hopscotch.startTour(tour);
    });
})(jQuery);
