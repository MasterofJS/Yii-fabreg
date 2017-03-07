import { API } from '../../../modules/config';
import moment from 'moment';
import CommentVote from './CommentVote';
import CommentMenu from './CommentMenu';

export default React.createClass({

    getInitialState(){
        return {
            likes: this.props.comment.likes,
            dislikes: this.props.comment.dislikes,
            liked: this.props.comment.liked,
            disliked: this.props.comment.disliked
        };
    },

    render() {
        const comment = this.props.comment;
        const self = this;
        return (
            <div className="comment_ indent_">
                <Link
                    to={`/user/${ comment.author.username }`}
                    className="av_"
                    style={{
                        backgroundImage: `url(${comment.author._links.avatar.href})`
                    }}
                >
                    {/*img src={ comment.author._links.avatar.href } alt={ comment.author.username }/*/}
                </Link>

                <div className="body_">

                    <Link
                        to={`/user/${ comment.author.username }`}
                        className="author_"
                    >
                        { comment.author.username }
                    </Link>

                    <span className="details_">
                        {this.state.likes}&nbsp;likes&nbsp;&middot;&nbsp;{this.state.dislikes}&nbsp;
                                                dislikes&nbsp;&middot;&nbsp;{moment.unix(/*-1 * (new Date()).getTimezoneOffset() + 60 +* */ comment.timestamp).format("LL")}</span>

                    {
                        auth.loggedIn() &&
                        <CommentMenu {...comment} rootThat={ this.props.rootThat } user={ this.props.user }/>

                    }

                    {/*                    <div className="menu_">

                     <a href="#" data-dropdown={`drop_${comment.id}`} className="dropdown">
                     <i className="icon-down-open"></i>
                     </a>

                     <ul id={`drop_${comment.id}`} data-dropdown-content className="f-dropdown" aria-hidden="true">
                     <li><a href="#">Report Comment</a></li>
                     <li><a href="#">Mute @{comment.author.username}</a></li>
                     </ul>


                     </div>
                     */}
                    {
                        !!(comment.type)
                            ?
                            <div className="text_">
                                <img src={comment.content} alt=""/>
                            </div>
                            :
                            <div className="text_">{comment.content}</div>
                    }

                    <a href="#"
                       className="reply_"
                       onClick={function(e){ e.preventDefault();self.props.handleReplyFormShow(comment);}}
                    >Responder</a>

                    <ul className="comment_vote_ no-bullet">
                        <li>
                            <a
                                href="#up"
                                className={classNames({
                                    'active': this.state.liked
                                })}
                                onClick={this.handleLike.bind(this, 'like')}>
                                <i className="icon-thumbs-up"></i>
                            </a>
                        </li>

                        <li>
                            <a
                                href="#down"
                                className={classNames({
                                    'active': this.state.disliked
                                })}
                                onClick={this.handleLike.bind(this, 'dislike')}>
                                <i className="icon-thumbs-down"></i>
                            </a>
                        </li>
                    </ul>

                </div>
            </div>
        );
    },

    handleLike(type, e){
        var oppositeType;
        e.preventDefault();

        if (!auth.loggedIn()) {
            APP.props.history.replaceState(null, '/login', {'ref': APP.props.location.pathname});
            return;
        }

        oppositeType = (type === 'like') ? 'dislike' : 'like';

        $.post(`${ API }/comments/${this.props.comment.id}/${type}`, function () {
            this.setState({
                [`${type}d`]: !this.state[`${type}d`],
                [`${type}s`]: this.state[`${type}d`] ? (this.state[`${type}s`] - 1) : (this.state[`${type}s`] + 1)
            });

            if (this.state[`${oppositeType}d`]) {

                this.setState({
                    [`${oppositeType}d`]: !this.state[`${oppositeType}d`],
                    [`${oppositeType}s`]: this.state[`${oppositeType}d`] ? (this.state[`${type}s`] - 1) : (this.state[`${type}s`] + 1)
                });
            }
        }.bind(this));
    }
});
