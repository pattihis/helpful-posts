/**
 * All of the code for our public-facing JavaScript source
 * should reside in this file.
 */

document.addEventListener('DOMContentLoaded', () => {

	const helpfulVotes = document.querySelector('.helpful-posts-wrap');

	// Run our scripts only if our frontend markup exists.
	if (helpfulVotes) {
		const postID = helpfulVotes.dataset.post;

		// Register our event listeners on the vote buttons.
		(document.querySelectorAll('.helpful-posts__buttons button') || []).forEach((button) => {
			const vote = button.dataset.vote;

			button.addEventListener('click', (e) => {
				e.preventDefault();

				document.getElementById('helpful-posts__loader').classList.remove('is-hidden');
				document.querySelector('.helpful-posts__buttons').classList.add('is-hidden');

				const data = new FormData();
				data.append('action', 'helpfulVote');
				data.append('nonce', helpfulPosts.nonce);
				data.append('vote', vote);
				data.append('postid', postID);

				// Our AJAX request to add votes.
				fetch(helpfulPosts.ajaxUrl, {
					method: 'POST',
					credentials: 'same-origin',
					body: data
				}).then((response) => {
					return response.json();
				}).then((data) => {
					if (data) {
						document.querySelector('.helpful-posts__title').innerHTML = helpfulPosts.thankYou;
						document.querySelector('.helpful-posts__buttons--' + vote).classList.add('selected');
						document.querySelector('.helpful-posts__buttons--yes').innerHTML = '<span>' + data.yes_perc + '</span>';
						document.querySelector('.helpful-posts__buttons--no').innerHTML = '<span>' + data.no_perc + '</span>';

						document.getElementById('helpful-posts__loader').classList.add('is-hidden');
						document.querySelector('.helpful-posts__buttons').classList.remove('is-hidden');
					}
				}).catch((error) => {
					console.log('[Helpful Posts Plugin]');
					console.error(error);
				});

			});
		});

		// Bust the cache by sending an AJAX request to the server.
		checkVoteDatabase(postID);

		function checkVoteDatabase(postID) {
			const data = new FormData();
			data.append('action', 'checkVoteDb');
			data.append('nonce', helpfulPosts.nonce);
			data.append('postid', postID);

			fetch(helpfulPosts.ajaxUrl, {
				method: 'POST',
				credentials: 'same-origin',
				body: data
			}).then((response) => {
				return response.json();
			}).then((data) => {
				if (data.status === 'voted') {
					// If the user has already voted show the results.
					document.querySelector('.helpful-posts__title').innerHTML = helpfulPosts.votedAlready;
					document.querySelector('.helpful-posts__buttons--' + data.vote).classList.add('selected');
					document.querySelector('.helpful-posts__buttons--yes').innerHTML = '<span>' + data.yes_perc + '</span>';
					document.querySelector('.helpful-posts__buttons--no').innerHTML = '<span>' + data.no_perc + '</span>';
				} else if (data.status === 'not_voted') {
					// If the user has not voted yet we show the buttons.
					document.querySelector('.helpful-posts__title').innerHTML = helpfulPosts.title;
					document.querySelector('.helpful-posts__buttons--yes').classList.remove('selected');
					document.querySelector('.helpful-posts__buttons--no').classList.remove('selected');
					document.querySelector('.helpful-posts__buttons--yes').innerHTML = '<button data-vote="yes">' + helpfulPosts.yesTxt  + '</button>';
					document.querySelector('.helpful-posts__buttons--no').innerHTML = '<button data-vote="no">' + helpfulPosts.noTxt + '</button>';
				}
			}).catch((error) => {
				console.log('[Helpful Posts Plugin]');
				console.error(error);
			});
		}
	}

});
