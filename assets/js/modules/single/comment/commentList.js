import Comment from './comment';
import Placehold from '../../placehold';


export default React.createClass({
    

    render() {
        let self = this;
        let loaded = this.props.loaded;
        let content = null;
        if (this.props.data === null) {

            content = (<Placehold/>);
            loaded = false;

        } else {

            content = this.props.data.map(function (comment) {

                return (
                    <Comment
                        key={ comment.id }
                        comment={comment}
                        onCommentSubmit={self.props.handleCommentSubmit}
                        rootThat={self.props.rootThat}
                        user={self.props.user}
                        post_id={self.props.post_id}
                    />
                );
            });

        }

        return (

            <div className="commentList">

                { content }

                { !!(this.props.linkNextPage && loaded) &&
                <div className="loadmore_">
                    <button className="button" onClick={this.handleLoadMore}>Abrir Mais</button>
                </div>
                }
            </div>
        )
            ;
    },

    handleLoadMore(e) {
        e.preventDefault();

        this.props.pushData();
    }
    
});
