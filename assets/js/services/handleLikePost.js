import {API} from '../modules/config';

export default handleLikePost;

let clicked = false;

function handleLikePost(id, type, event) {
    let oppositeType;

    event.preventDefault();

    if (!auth.loggedIn()) {
        APP.props.history.replaceState(null, '/login', {'ref': APP.props.location.pathname});
        return;
    }

    if (clicked) {
        return;
    }

    oppositeType = (type === 'like') ? 'dislike' : 'like';

    clicked = true;

    $.post(`${ API }/posts/${ id }/${type}`, function () {
        clicked = false;

        this.setState({
            [`${type}d`]: !this.state[`${type}d`],
            [`${type}s`]: this.state[`${type}d`] ? (this.state[`${type}s`] - 1) : (this.state[`${type}s`] + 1)
        });

        if (this.state[`${oppositeType}d`]) {

            this.setState({
                [`${oppositeType}d`]: false,
                [`${oppositeType}s`]: (this.state[`${oppositeType}s`] - 1)
            });
        }

    }.bind(this));
}
