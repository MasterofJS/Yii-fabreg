import { ROOT, API } from '../../modules/config';
import Navigation from '../single/navigation';
import Footer from '../single/footer';
import DocumentMeta from '../../services/documentMeta.react';
import ImageLoader from '../../services/react-imageloader';
import handleLikePost from '../../services/handleLikePost';
import {isMobile, isiOS} from '../../services/isiOS';


const Preloader = function () {
    return (<div className="placehold_">
        <i className="icon-spin6 animate-spin"></i>
    </div>)
}

export default React.createClass({

    getInitialState(){
        return {
            liked: this.props.data.liked,
            disliked: this.props.data.disliked,
            likes: this.props.data.likes,
            dislikes: this.props.data.dislikes
        }
    },

    componentWillMount(){
        const data = this.props.data;
        // todo check
        const meta = {
            title: `${data.description} | Unicorno`,
            description: data.title,
            canonical: `${ ROOT }posts/${this.props.id}`,
            keywords: data.keywords,
            image: data.picture || `${ ROOT }dist/images/logo.jpg`
        };

        DocumentMeta(meta);
    },


    render() {
        const data = this.props.data;

        return (
            <div className="post_">

                <header className="row collapse">
                    <div className="column">

                        <h1>{data.description}</h1>

                        <p className="amount_ left">
                            <span>{this.state.likes}&nbsp;likes&nbsp;&middot;&nbsp;</span>
                            <span>{this.state.dislikes}&nbsp;dislikes&nbsp;&middot;&nbsp;</span>
                            <span>{data.comments || 0}&nbsp;comentários</span>
                        </p>

                        <Navigation prev={data.prevPost} next={data.nextPost}/>

                        <div className="bar_">
                            <ul className="actions_ button-group">
                                <li>
                                    <a href="#up"
                                       className={classNames({
                                           'button': true,
                                            'active': this.state.liked
                                        })}
                                       onClick={handleLikePost.bind(this, this.props.id, 'like')}
                                    >
                                        <i className="icon-thumbs-up"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="#down"
                                       className={classNames({
                                           'button': true,
                                            'active': this.state.disliked
                                        })}
                                       onClick={handleLikePost.bind(this, this.props.id, 'dislike')}
                                    >
                                        <i className="icon-thumbs-down"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </header>
                <div className="row collapse">
                    <div className="column text-center">

                        <SinglePicture
                            data={data}
                            withoutWrap={true}/>
                    </div>
                </div>

                <Footer {...data}/>

                <hr/>
            </div>
        );
    }

});

const SinglePicture = React.createClass({

    getInitialState () {
        return {
            loaded: false
        }
    },

    render () {
        let classImg = classNames({
            'pic_': true,
            'loading_': !this.state.loaded,
            'gif_': this.props.data.type === 'gif'
        });
        return (
            <div className={classImg}>
                {
                    this.props.data.type !== 'gif'
                        ?
                        <ImageLoader
                            src={ this.props.data.type === 'long_image' ? this.props.data._links.long_image.href : this.props.data._links.photo.href}
                            preloader={ Preloader }
                            onLoad={ this.handleLoad }
                            imgProps={{
                            'alt': this.props.data.description
                        }}
                        >
                            Image load failed!
                        </ImageLoader>
                        :
                        <SingleVideo src={this.props.data._links}/>
                }
            </div>
        );
    },

    handleLoad() {
        this.setState({
            loaded: true
        })
    }
});

const SingleVideo = React.createClass({
    
    getInitialState () {
        return {
            paused: true
        };
    },

    handlePause(){
        this.setState({
            paused: true
        });
    },

    handlePlay(){
        this.setState({
            paused: false
        });
    },

    componentDidMount(){
        this.video = ReactDOM.findDOMNode(this.refs.video);

        this.video.addEventListener('pause', this.handlePause);
        this.video.addEventListener('play', this.handlePlay);

        if (!this.isMobile) {
            this.video.play();
        }
    },

    componentWillUnmount(){
        this.video.removeEventListener('pause', this.handlePause);
        this.video.removeEventListener('play', this.handlePlay);
    },

    isMobile: isMobile(),

    render () {
        return (
            <div className="gif_wrap_">
                <video
                    preload="auto"
                    ref="video"
                    poster={`${this.props.src.photo.href}`}
                    loop
                    autoPlay={ !this.isMobile }
                    muted=""
                    controls={ false }
                >
                    <source src={`${this.props.src.video.mp4.href}`} type="video/mp4"/>
                    <source src={`${this.props.src.video.webm.href}`} type="video/webm"/>
                </video>
                {
                    this.isMobile &&
                    <div
                        className={classNames({
                        'control_': true,
                        'paused_': this.state.paused
                    })}
                        onClick={this._handleClick}
                    >
                    <span>GIF</span>
                </div>
                }
            </div>
        );
    },

    _handleClick(){
        if (this.state.paused) {
            this.video.play();
        } else {
            this.video.pause();
        }
    }

});