import React from 'react';
export default React.createClass({

    render () {
        return (
            <a className="load_more_comments_" href="#" onClick={this.props.onLoad}>Load More Replies</a>
        );
    }

});
