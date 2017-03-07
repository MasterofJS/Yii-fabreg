import React from 'react';
import Comment from './commentNested';

//import CommentForm from './commentForm';

const CommentListNested = React.createClass({

    getDefaultProps() {
        return {
            handleChildReply: null
        };
    },

    render() {
        var commentNodes;
        let self = this;
        commentNodes = this.props.data.map(function (comment) {
            return (
                <CommentNested
                    key={comment._id}
                    comment={comment}
                    nested={true}
                    handleReplyShow={self.props.handleChildReply}
                    onCommentSubmit={null}
                >
                    {comment.text}
                </CommentNested>
            );
        });
        return (
            <div className="commentList">
                {commentNodes}
            </div>
        );
    }

});

export default CommentListNested;