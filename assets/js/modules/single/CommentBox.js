import moment from 'moment';
import { ROOT, API } from '../../modules/config';
import sendComment from './comment/sendComment';
import Placehold from '../placehold';
import CommentList from './comment/commentList';
import CommentForm from './comment/commentForm';
//import CommentReportForm from './comment/commentReportForm';
//import {TriggerModal} from '../../modules/modal.react';
import paginationBuider from '../../services/paginationBuilder';
require('es6-object-assign').polyfill();


export default React.createClass({

    getInitialState () {
        return {
            data: [],
            loaded: false,
            typeComments: 'hot',
            linkNextPage: `${ API }/posts/${this.props.id}/comments`,
            report: this.props.can_viewer_report
        };
    },

    loadCommentsFromServer (t) {
        //let index = i || 0;
        let type = t || this.state.typeComments;

        if (!this.state.linkNextPage) {
            return;
        }
        this.setState({
            loaded: false
        });
        $.ajax({
            url: `${this.state.linkNextPage}/${type}`,
            dataType: 'json',
            cache: false,
            data: {
                expand: 'author,avatar,permissions'
            },
            success: function (respond, textStatus, request) {
                let newData, nextLink;
                nextLink = paginationBuider(request.getResponseHeader('Link'));

                newData = Array.prototype.concat(this.state.data, respond);

                this.setState({
                    data: newData,
                    linkNextPage: nextLink,
                    loaded: true
                });
            }.bind(this),
            error: function (xhr, status, err) {
                console.error(this.props.url, status, err.toString());
            }.bind(this)
        });
    },

    handleCommentSubmit (comment) {
        var newComment;
        let isMedia = /^https?:\/\/(?:[a-z0-9\-]+\.)+[a-z]{2,6}(?:\/[^/#?]+)+\.(?:jpg|gif|png)$/ig;

        newComment = {
            post_id: this.props.id,
            content: comment.text
        };

        if (Array.isArray(comment.text.match(isMedia))) {
            newComment.type = 1;
        }

        $.ajax({
            url: `${ API }/comments`,
            method: 'post',
            data: newComment,
            success: function (respond) {
                let newComments;
                respond = Object.assign(respond, {author: this.props.user});
                newComments = Array.prototype.concat(
                    respond,
                    this.state.data
                );
                this.setState({data: newComments});
            }.bind(this),
            error: function (error) {
                console.warn(error);
            }
        });
    },

    componentDidMount () {
        this.loadCommentsFromServer(this.state.typeComments);
    },

    render () {
        return (
            <div className="comment_box_">
                <header className="head_">
                    <h1 className="title_ left">{this.props.amount}&nbsp;Comentários</h1>
                    <ul className="inline-list right">
                        <li>
                            <a
                                href="#"
                                className={classNames({'active': (this.state.typeComments === 'hot')})}
                                onClick={this.handleLoadHot}>Top</a>
                        </li>

                        <li>
                            <a
                                href="#"
                                className={classNames({'active': (this.state.typeComments === 'fresh')})}
                                onClick={this.handleLoadFresh}>Novos</a>
                        </li>

                    </ul>

                    <hr/>
                </header>

                {<CommentForm
                    user={ this.props.user }
                    onCommentSubmit={ this.handleCommentSubmit }
                    max={500}
                />}

                {(this.state.data.length > 0) &&
                <CommentList
                    data={this.state.data}
                    user={this.props.user}
                    loaded={this.state.loaded}
                    handleCommentSubmit={this.handleCommentSubmit}
                    rootThat={this}
                    count={this.state.countPages}
                    pushData={this.pushData}
                    post_id={this.props.id}
                    linkNextPage={this.state.linkNextPage}
                />}

            </div>
        );
    },



    handleLoadHot(e) {
        e.preventDefault();
        this.setState({
            typeComments: 'hot',
            linkNextPage: `${ API }/posts/${this.props.id}/comments`,
            data: []
        }, function () {
            this.loadCommentsFromServer('hot');
        });
    },

    handleLoadFresh(e) {
        e.preventDefault();

        this.setState({
            typeComments: 'fresh',
            linkNextPage: `${ API }/posts/${this.props.id}/comments`,
            data: []
        }, function () {
            this.loadCommentsFromServer('fresh');
        });
    },

    pushData (index, cb){
        this.loadCommentsFromServer(this.state.typeComments);
    }

});
