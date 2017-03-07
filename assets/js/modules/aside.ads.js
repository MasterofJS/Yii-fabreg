// function shuffle(array) {
//     var currentIndex = array.length, temporaryValue, randomIndex ;
//
//     // While there remain elements to shuffle...
//     while (0 !== currentIndex) {
//
//         // Pick a remaining element...
//         randomIndex = Math.floor(Math.random() * currentIndex);
//         currentIndex -= 1;
//
//         // And swap it with the current element.
//         temporaryValue = array[currentIndex];
//         array[currentIndex] = array[randomIndex];
//         array[randomIndex] = temporaryValue;
//     }
//
//     return array;
// }
//
// module.exports.init = function () {
//     $.ajax({
//         url: 'data/ads.json',
//         success: function (respond) {
//             var template = Handlebars.compile($('#tpl_aside_ads_').html());
//             $('#aside_ads_').append(template(shuffle(respond).slice(0,7)));
//         },
//         error: function (error) {
//             console.warn(error);
//         }
//     });
// };