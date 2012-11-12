jQuery(function () {

    var $votes = $('div.vote');
    if (!$votes.length) return;

    // collect all needed vote items from the page
    var request = [];
    for (var i = 0; i < $votes.length; i++) {
        var $item = $($votes[i]);

        // store info
        request[request.length] = $item.attr('data-id');
    }

    // send request to backend
    $.post(
        SITE_BASE+'vote',
        {request:request},
        function (data) {
            // apply votes everwhere
            for (var j = 0; j < $votes.length; j++) {
                var $item = $($votes[j]);
                var id    = $item.attr('data-id');
                var votes = 0;
                var mine  = 0;
                if(data[id]){
                    votes = data[id]['votes'];
                    mine  = data[id]['mine'];
                }

                vote_init($item,votes,mine);
            }
        }
    )
});

/**
 * Fill a vote box with the actual vote numbers and controls
 *
 * @param $box
 * @param votes
 * @param mine
 */
function vote_init($box,votes,mine) {
    var html = '';
    var minedown = '';
    var mineup = '';
    if (mine === -1) minedown = ' mine';
    if (mine === 1) mineup = ' mine';



    html += '<a class="vote-up' + mineup + '" title="Good idea!">⋀</a>';
    if(votes >= 0)
        html += '<div class="votes">' + votes + '</div>';
    else
    html +=  '<div class="votes minus">' + votes + '</div>';



    html += '<a class="vote-down' + minedown + '" title="Bad idea!">⋁</a>';


    $box.html(html);
    $box.find('a').click(vote_cast);
}

/**
 * Cast a voting
 */
function vote_cast(){
    var $thumb = $(this);
    var $box   = $(this.parentNode);
    var vote   = 1;

    if($thumb.hasClass('vote-down')) vote = -1;
    $box.html('<img src="'+SITE_BASE+'/img/ajax-loader.gif" width="11" height="16" alt="Lade.." />');
    $.post(
        SITE_BASE+'vote/cast/'+$box.attr('data-id'),
        {vote: vote },
        function(data){
            vote_init($box,data['votes'],data['mine']);
            if(data.error == 1){
                $('div.messages div.nologin-error').remove();
                $('div.messages').append('<div class="alert alert-error nologin-error"><button type="button" class="close" data-dismiss="alert">×</button>Please login to cast votes.</div>');
                $('div.messages div.nologin-error')[0].scrollIntoView();
            }
        }
    )
}

