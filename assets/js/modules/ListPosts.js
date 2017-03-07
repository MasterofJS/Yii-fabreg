import { API, GOOGLE_AD } from '../modules/config';
import Placehold from './placehold';
import ListItem from './list/item';
import paginationBuider from  '../services/paginationBuilder';
const InfiniteScroll = require('react-infinite-scroll2')(React, ReactDOM);
import GoogleAd from 'react-google-ad';
import ScrollMagic from 'scrollmagic';


const controller = new ScrollMagic.Controller();
//let sceneGoToTop = null;

const InfiniteList = React.createClass({

    getInitialState () {
        const data = this.props.data.slice();
        const length = data.length;
        return {
            data: data,
            elements: this.buildElements(0, length > 20 ? 20 : length, data),
            isInfiniteLoading: false,
            loadMoreText: length > 20 && 'Carregando....',
            next: this.props.next,
            nextLink: this.props.src
        }

    },

    buildElements (start, end, data = this.state.data) {
        var i;
        let elements = [];

        for (i = start; i < end; i++) {
            if (!data[i]) break;
            elements.push(<ListItem key={i} data={data[i]}/>)
        }

        // if mobile splice with google ad
        if (Foundation.utils.is_small_only()) {

            [9, 20].forEach(function (a) {
                elements.splice(a, 0,
                    <div
                        className="googlead_"
                        key={Date.now() * Math.random()}>
                        <GoogleAd
                            client={ GOOGLE_AD.client }
                            slot={ GOOGLE_AD.slot }
                            format="rectangle"
                        />
		    </div>
                );
            });
        }

        return elements;
    },

    loadData () {
        var that = this;

        if (!this.state.nextLink) {
            return;
        }

        $.ajax({
            url: this.state.nextLink,
            data: {
                'per-page': 40
            },
            success (respond, t, request) {

                let nextLink = paginationBuider(request.getResponseHeader('Link'));
                that.setState({
                    nextLink: nextLink,
                    data: that.state.data.concat(respond),
                    isInfiniteLoading: false
                }, function () {
                    that.handleInfiniteLoad();
                });
            }
        });

    },

    handleInfiniteLoad () {
        var elemLength;
        var newElements;
        var that;
        that = this;

        elemLength = this.state.elements.length;

        if (0 === elemLength % 20) {
            if (0 === elemLength % this.props.perPage && !that.state.next) {

                that.setState({
                    isInfiniteLoading: false
                });
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
                threshold={500}
            >

                {this.state.elements}


                {this.state.nextLink &&
                <div className="loadmore_">
                    <button className="button" onClick={this.loadData}>Quero mais!</button>
                </div>}

            </InfiniteScroll>)
    }

});


export default React.createClass({
    getInitialState(){
        return {
            next: !!this.props.linkNextPage,
            data: this.props.data,
            goToActive: false
        }
    },

    componentDidMount() {
        var self = this;

        this.sceneGoToTop = new ScrollMagic.Scene({
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
        this.sceneGoToTop.destroy();
        this.sceneGoToTop = null;
    },

    render () {
        var goToActive = classNames({
            'go_to_top_': true,
            'active': this.state.goToActive
        });
        var content = (<InfiniteList
            src={ this.props.linkNextPage }
            next={ !!this.state.next }
            data={this.state.data}
            perPage={this.props.perPage}
        />);

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
