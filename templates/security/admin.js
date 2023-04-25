const usersButtonLink = document.getElementById('users-Button-Link');
const tasksButtonLink = document.getElementById('tasks-Button-Link');
const usersDiv = document.getElementById('users');
const tasksDiv = document.getElementById('tasks');

usersButtonLink.addEventListener('click', () => {
	usersDiv.style.display = 'block';
	tasksDiv.style.display = 'none';
	usersButtonLink.className = 'active';
	tasksButtonLink.className = '';
	return false;
});

tasksButtonLink.addEventListener('click', () => {
	usersDiv.style.display = 'none';
	tasksDiv.style.display = 'block';
	tasksButtonLink.className = 'active';
	usersButtonLink.className = '';
	return false;
});
