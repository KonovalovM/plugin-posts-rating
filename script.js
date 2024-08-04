jQuery(document).ready(function($) {
    $(document).on('click', '.rating-panel .star', function() {
        var $panel = $(this).closest('.rating-panel');
        var post_id = $panel.data('post-id');
        var rating = $(this).data('value');

        $.ajax({
            url: ajax_obj.ajax_url,
            type: 'POST',
            data: {
                action: 'rate_post',
                post_id: post_id,
                rating: rating
            },
            success: function(response) {
                if (response.success) {
                    var new_rating = response.data.new_rating;
                    $panel.siblings('.post-rating').text('Rating: ' + new_rating.toFixed(2));
                } else {
                    alert('Error: ' + response.data);
                }
            },
            error: function(response) {
                alert('An error occurred.');
            }
        });
    });
});
