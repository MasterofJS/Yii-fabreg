import Placehold from './placehold';
import ListItem from './list/item';
const InfiniteScroll = require('react-infinite-scroll2')(React, ReactDOM);
import GoogleAd from 'react-google-ad';
import ScrollMagic from 'scrollmagic';
const controller = new ScrollMagic.Controller();
let sceneGoToTop = null;
const InfiniteList = React.createClass({

    getInitialState () {
        const data = this.props.data.slice();
        const length = 100;
        return {
            data: data,
            elements: this.buildElements(0, length > 20 ? 20 : length, data),
            isInfiniteLoading: false,
            loadMoreText: length > 20 && 'Carregando....',
            next: this.props.next
        }

    },

    buildElements (start, end, data = this.state.data) {

        var i;
        let elements = [];

        for (i = start; i < end; i++) {
            elements.push(<ListItem key={i} data={data[i]}/>)
        }

        // if mobile splice with google ad
        if (Foundation.utils.is_small_only()) {

            [9, 20].forEach(function (a) {
                elements.splice(a, 0,
                    (<div
                        className="googlead_"
                        key={Date.now() * Math.random()}>
                        <GoogleAd
                            client="ca-pub-1665577023063217"
                            slot="7960662881"
                            format="rectangle"/>
                    </div>)
                );
            });
        }

        return elements;
    },

    loadData () {
        var that = this;

        $.getJSON(this.props.src, function (respond) {
            that.setState({
                next: respond.next,
                data: that.state.data.concat(respond.list),
                isInfiniteLoading: false
            });

            that.handleInfiniteLoad();
        });

    },

    handleInfiniteLoad () {
        var elemLength;
        var newElements;
        var that;

        that = this;

        elemLength = this.state.elements.length;
        if (0 === elemLength % 20) {

            if (0 === elemLength % 100 && !that.state.next) {

                that.setState({
                    isInfiniteLoading: false
                });
                return;
            }
            this.setState({
                isInfiniteLoading: true
            });

        }
        if (elemLength < this.state.data.length) {
            newElements = that.buildElements(elemLength, elemLength + 20);
            that.setState({
                isInfiniteLoading: false,
                elements: that.state.elements.concat(newElements)
            });
        } else {
            // AJAX
        }
    },

    elementInfiniteLoad () {
        return (
            <div className="infinite-list-item">
                {this.state.loadMoreText}
            </div>
        );
    },

    render () {
        return (
            <InfiniteScroll
                pageStart={0}
                loadMore={this.handleInfiniteLoad}
                hasMore={!this.state.isInfiniteLoading}
                loader={<div className="infinite-list-item"> {this.state.loadMoreText} </div>}
                threshold={500}
            >

                {this.state.elements}
                {this.state.next && <div className="loadmore_">
                    <button className="button" onClick={this.loadData}>Load More</button>
                </div>}

            </InfiniteScroll>)
    }

});

export default React.createClass({

    getInitialState(){
        return {
            //next: false,
            data: this.props.data,
            goToActive: false
        }
    },

    /* componentWillReceiveProps(nextProps){
     let self = this;

     if (this.props.src !== nextProps.src) {
     self.setState({
     data: null
     });
     $.getJSON(nextProps.src, function (respond) {
     self.setState({
     next: respond.next,
     data: respond.list
     });
     });

     }
     },*/

    componentDidMount() {
        var self;
        self = this;

        /*        $.getJSON(this.props.src, function (respond) {

         self.setState({
         next: respond.next,
         data: respond.list
         });
         self.props.getLength && self.props.getLength(respond.count);

         });*/

        sceneGoToTop = new ScrollMagic.Scene({
            offset: window.innerHeight,
            triggerHook: 0
        })
            .on('enter', function () {
                self.setState({
                    goToActive: true
                });
            })
            .on('leave', function () {
                self.setState({
                    goToActive: false
                });
            })
            .addTo(controller);

    },

    componentWillUnmount() {
        sceneGoToTop.destroy();
        sceneGoToTop = null;
    },

    render () {
        var goToActive = classNames({
            'go_to_top_': true,
            'active': this.state.goToActive
        });
        var content = null;

        //if (null === this.state.data) {

        //content = (<Placehold/>);

        //} else {

        content = (<InfiniteList src={ this.props.linkNextPage } data={this.state.data}/>);

        //}

        return (
            <div className="list_" id="list_">
                {content}

                <a href="#" onClick={this._goToTop} className={goToActive}></a>
            </div>
        );
    },

    _goToTop(e){
        e.preventDefault();

        $('body,html').animate({
            scrollTop: 0
        }, 600);
    }

});
