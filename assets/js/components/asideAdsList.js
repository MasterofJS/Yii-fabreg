import {API, GOOGLE_AD} from "../modules/config";
import ScrollMagic from "scrollmagic";
// import GoogleAd from "../services/GoogleAd";
import GoogleAd from 'react-google-ad';

const InfoAds = React.createClass({

    render () {
        return (
            <div className="info_">
                <p>{this.props.data.title}</p>
            </div>
        );
    }
});

const ItemPost = React.createClass({

    render () {
        return (
            <div className='picture'>
                <div className="img_">
                    <Link
                        target="_blank"
                        to={`/posts/${ this.props.data.id }`}
                        className="badge_">
                        <img src={ this.props.data.src } alt={ this.props.data.title }/>
                    </Link>
                </div>
                <InfoAds data={ this.props.data }/>
            </div>

        );
    }

});

export default React.createClass({

    getInitialState () {
        return {
            pictures: []
        };
    },

    componentWillMount () {
        $.getScript('//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js',()=>{
            console.log('start');});
    },

    componentDidMount () {

        var self = this;

        var url = `${ API }/posts/featured`;

        if (Foundation.utils.is_small_only()) {
            return null;
        }


        $.getJSON(url, function (result) {

            var pictures;
            if (!result || !result.length) {
                return;
            }

            pictures = result.map((p) => {
                result = {
                    id: p.id,
                    src: p._links.photo.href,
                    title: p.description
                };
                return result;
            });


            if (self.isMounted()) {
                setTimeout(function () {


                    // $.getScript('//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js');

                }, 150);

                self.setState({
                    pictures: pictures
                });
            }
        });

    },

    componentWillUnmount(){
        $(window).off('resize', self.onResize);
        if (this.controller) {
            this.controller.destroy(true);
        }
    },

    place: [0, 10, 20, 30, 40, 50, 60, 70],

    render () {
        let pictures;

        if (Foundation.utils.is_small_only()) {
            return null;
        }

        if (this.state.pictures.length <= 0) {
            pictures = <p>Carregando imagens...</p>;
        } else {
            pictures = this.state.pictures.map(function (p, i) {
                return <ItemPost key={p.id} data={p}/>
            });
            if (this.place.length < 8) {
                this.place.push(this.state.pictures.length + 7);
            }
            this.place.forEach((a, i) => {
                pictures.splice(a, 0,
                    <div className="googlead_" key={i}>
                        <GoogleAd
                            client={ GOOGLE_AD.client }
                            slot={ GOOGLE_AD.slot }
                            format="rectangle"
                        />
                    </div>
                )
            });
        }
        return (

            <div className="pictures"> {pictures} </div>

        );
    }
});

