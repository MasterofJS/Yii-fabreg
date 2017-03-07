import { viaTwitter, ROOT, ORIGIN, API } from '../modules/config';
const FacebookButton = React.createClass({

    render () {
        return (
            <a
                href="#"
                role="button"
                className={ this.props.className }
                onClick={this._share}
            >
                { this.props.children }
            </a>
        );
    },

    _share (e) {
        const id = this.props.id;

        e.preventDefault();
        // debugger;
        FB.ui({
            method: 'feed',
            link: `${ ORIGIN }/posts/${ id }`,
            name: this.props.description,
            // caption: window.location.host,
            description: 'Clique aqui para ver a imagem e deixar um comentário',
            source: this.props.photo,
            picture: this.props.photo,
            display: 'popup'
        }, function (response) {
            if (response) {
                $.post(`${ API }/posts/${id}/share`, {
                    'network': 1 //facebook
                });
            }
        });
    }

});


const TwitterButton = React.createClass({

    render () {
        return (
            <a
                onClick={ this._share }
                href={`https://twitter.com/intent/tweet?url=${ ORIGIN }/posts/${ this.props.id }&text=${ this.props.text }&via=${ viaTwitter }`}
                className={ this.props.className }
            >
                { this.props.children }
            </a>
        );
    },

    _share(){
        $.post(`${ API }/posts/${ this.props.id }/share`, {
            'network': 3 //twitter
        });
    }

});

export { FacebookButton, TwitterButton };
