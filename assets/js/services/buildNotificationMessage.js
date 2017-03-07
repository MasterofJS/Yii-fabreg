import moment from 'moment';
import 'moment/locale/pt-br.js';
export default {
    buildMessage,
    getIcon,
    getFromNow
}

function buildMessage(data) {

    /*

     comment_post
     <paul> commented on your post
     <paul> and <buba> commented on your post
     <paul> and <buba> and 3 other commented on your post

     like_post
     <paul> liked your post
     <paul> and <buba> liked your post
     <paul> and <buba> and 3 other liked your post

     share_post
     <paul> shared your post
     <paul> and <buba> shared your post
     <paul> and <buba> and 3 other shared your post

     like_comment
     <paul> liked on your comment
     <paul> and <buba> liked on your comment
     <paul> and <buba> and 3 other liked on your comment

     reply_comment
     <paul> replied to your comment
     <paul> and <buba>  replied to your comment
     <paul> and <buba>  and 3 other replied to your comment


     trending_post
     your post is trending now


     hot_post
     your post is hot

     */

    const type = data.type,
        user1 = data.actors[0],
        user2 = data.actors[1],
        count = data.count;
    let message = null;

    if (type === 'trending_post') {

        message = <p>'Parabéns, seu post foi enviado para a página "Em Alta"'</p>;//`Your post is trending now`;

    }
    else if (type === 'hot_post') {

        message = <p>'Parabéns, seu post foi enviado para a página "Top"'</p>;

    }
    else if (!user2) {

        switch (type) {
            case 'comment_post':

                message = <p>{ wrapName(user1.username) } comentou no seu post</p>;

                break;
            case 'like_post':

                message = <p>{ wrapName(user1.username) } deu like no seu post</p>;

                break;
            case 'share_post':

                message = <p>{ wrapName(user1.username) } compartilhou seu post</p>;

                break;
            case 'like_comment':

                message = <p>{ wrapName(user1.username) } deu like no seu comentário</p>;

                break;
            case 'reply_comment':

                message = <p>{ wrapName(user1.username) } respondeu seu comentário</p>;

                break;

        }

    } else if (data.count > 0) {

        switch (type) {
            case 'comment_post':

                message =
                    <p>{ wrapName(user1.username) }, { wrapName(user2.username) } e { count } outras pessoas comentaram no seu post</p>;

                break;
            case 'like_post':

                message =
                    <p>{ wrapName(user1.username) }, { wrapName(user2.username) } e { count } outras pessoas deram like no seu post</p>;

                break;
            case 'share_post':

                message =
                    <p>{ wrapName(user1.username) }, { wrapName(user2.username) } e { count } outras pessoas compartilharam o seu post</p>;

                break;
            case 'like_comment':

                message =
                    <p>{ wrapName(user1.username) }, { wrapName(user2.username) } e { count } outras pessoas deram like no seu comentário</p>;

                break;
            case 'reply_comment':

                message =
                    <p>{ wrapName(user1.username) }, { wrapName(user2.username) } e { count } outras pessoas responderam seu comentário</p>;

                break;
        }

    } else if (user2) {

        switch (type) {
            case 'comment_post':

                message = <p>{ wrapName(user1.username) } e { wrapName(user2.username) } comentaram no seu post</p>;

                break;
            case 'like_post':

                message = <p>{ wrapName(user1.username) } e { wrapName(user2.username) } deram like no seu post</p>;

                break;
            case 'share_post':

                message = <p>{ wrapName(user1.username) } e { wrapName(user2.username) } compartilharam seu post</p>;

                break;
            case 'like_comment':

                message =
                    <p>{ wrapName(user1.username) } e { wrapName(user2.username) } deram like no seu comentário</p>;

                break;
            case 'reply_comment':

                message = <p>{ wrapName(user1.username) } e { wrapName(user2.username) } responderam seu comentário</p>;

                break;
        }

    }

    return message
}

function getIcon(type) {
    let icon = null;

    switch (type) {
        case 'comment_post':
        case 'reply_comment':

            icon = <i className="icon-comment"></i>;

            break;
        case 'like_post':
        case 'like_comment':

            icon = <i className="icon-thumbs-up"></i>;

            break;
        case 'share_post':

            icon = <i className="icon-share-squared"></i>;

            break;
        default:
            icon = null;
    }
    return icon;
}

function getFromNow(timestamp) {
    //return moment.unix(-1 * (new Date()).getTimezoneOffset() * 60 + timestamp).fromNow();
    return moment.unix(timestamp).fromNow();
}


/* utils */

function wrapName(name) {
    return <strong>{ name }</strong>
}
global.moment = moment;