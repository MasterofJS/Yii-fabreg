import {GOOGLE_AD} from "../modules/config";
import Header from './header';
import Picture from './picture';
import Share from './share';
import handleLikePost from '../../services/handleLikePost';
import GoogleAd from 'react-google-ad';

export default React.createClass({

    getInitialState () {
        return {
            likes: this.props.data.likes,
            dislikes: this.props.data.dislikes,
            liked: this.props.data.liked,
            disliked: this.props.data.disliked
        }
    },

    render () {
        const data = this.props.data;
        let classLiked = classNames({
            'button': true,
            'active': this.state.liked
        });
        let classDisliked = classNames({
            'button': true,
            'active': this.state.disliked
        });

        return (<article className="infinite-list-item">

            <Header
                date={data.timestamp}
                title={data.description}
                link={`/posts/${data.id}`}
            />

            {
                (+data.is_nsfw === 1) && (+APP.state.user.show_nswf !== 1) ?
                    <Link target="_blank" to={`/posts/${data.id}`} className="nsfw_">
                        <h4 className="title_">Não Abrir No Trabalho (NANT)</h4>
                        <span className="subtitle_">Clique para ver o post.</span>
                    </Link>
                    :
                    <Picture
                        src={ data._links.photo.href }
                        links={ data._links }
                        type={ data.type }
                        link={ `/posts/${data.id}` }
                    />
            }
            console.log('s');
			<textarea class="comment_" placeholder="Comment"></textarea>
            <footer className="button-bar left">
                <div className="googlead_">
                    <GoogleAd
                        client={ GOOGLE_AD.client }
                        slot={ GOOGLE_AD.slot }
                        format="rectangle"
                    />
                </div>
                <ul className="actions_ button-group">
                    <li>
                        <a
                            href="#up"
                            onClick={handleLikePost.bind(this, data.id, 'like')}
                            className={classLiked}
                        >
                            <i className="icon-thumbs-up"></i>
                        </a>
                    </li>
                    <li>
                        <a href="#down"
                           onClick={handleLikePost.bind(this, data.id, 'dislike')}
                           className={classDisliked}
                        >
                            <i className="icon-thumbs-down"></i>
                        </a>
                    </li>
                    <li>
                        <Link target="_blank" to={`/posts/${data.id}`} className="button">
                            <i className="icon-comment-empty"></i>
                        </Link>
                    </li>
                </ul>

                <Share {...data}/>

                <p className="amount_">
                    { this.state.likes }&nbsp;likes&nbsp;
                    &middot;&nbsp;{ this.state.dislikes }&nbsp;dislikes&nbsp;
                    &middot;&nbsp;<Link target="_blank" to={`/posts/${data.id}`}>{ data.comments }&nbsp;comentários</Link>
                </p>
            </footer>
            <hr/>
        </article>)
    }

});
