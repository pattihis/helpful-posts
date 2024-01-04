/**
 * All of the code for our admin-facing JavaScript source
 * should reside in this file.
 */

document.addEventListener('DOMContentLoaded', () => {

	const clearHelpfulVotes = document.getElementById('deleteHelpfulVotes');
	if (clearHelpfulVotes) {
		const postID = clearHelpfulVotes.dataset.post;
		const nonce = clearHelpfulVotes.dataset.nonce;

		// Register our event listener on the clear button.
		clearHelpfulVotes.addEventListener('click', (e) => {
			e.preventDefault();

			document.querySelector('.helpful-posts-metabox-wrap').innerHTML = '<div class="helpful-loader"></div>';

			const data = new FormData();
			data.append('action', 'helpfulVoteClear');
			data.append('nonce', nonce);
			data.append('postid', postID);

			// Our AJAX request to clear post votes.
			fetch(helpfulPosts.ajaxUrl, {
				method: 'POST',
				credentials: 'same-origin',
				body: data
			}).then((response) => {
				return response.json();
			}).then((data) => {
				if (data.success) {
					document.querySelector('#helpful-posts-votes .inside').innerHTML = '<h4>' + helpfulPosts.clearText + ' 0 </h4>';
				}
			}).catch((error) => {
				console.log('[Helpful Posts Plugin]');
				console.error(error);
			});
		});
	}

});
