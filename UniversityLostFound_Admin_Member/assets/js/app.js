
const searchBox = document.getElementById('searchBox');
const searchResults = document.getElementById('searchResults');

if (searchBox) {
    loadItems('');
    searchBox.addEventListener('keyup', function () {
        loadItems(searchBox.value);
    });
}

function loadItems(keyword) {
    fetch('../Controller/ajax.php?action=search&keyword=' + encodeURIComponent(keyword))
        .then(response => response.json())
        .then(data => {
            searchResults.innerHTML = '';

            if (!data.success || data.items.length === 0) {
                searchResults.innerHTML = '<p class="small">No items found.</p>';
                return;
            }

            data.items.forEach(item => {
                if (item.item_type !== 'Found' || item.status === 'Returned' || item.status === 'Rejected') {
                    return;
                }

                const box = document.createElement('div');
                box.className = 'list-row';

                box.innerHTML =
                    '<strong>' + escapeHtml(item.item_name) + '</strong> - ' +
                    escapeHtml(item.category) + '<br>' +
                    '<span class="small">Location: ' +
                    escapeHtml(item.location) + ' | Status: ' +
                    escapeHtml(item.status) + '</span>' +
                    '<br><button onclick="sendClaim(' + item.item_id + ')">Claim This Item</button>';

                searchResults.appendChild(box);
            });

            if (searchResults.innerHTML === '') {
                searchResults.innerHTML = '<p class="small">No approved found items are available.</p>';
            }
        })
        .catch(() => {
            searchResults.innerHTML = '<p class="error">AJAX search could not load.</p>';
        });
}

function sendClaim(itemId) {
    const message = prompt('Why do you think this item belongs to you?');

    if (message === null) {
        return;
    }

    if (message.trim() === '') {
        alert('Please write a reason for your claim.');
        return;
    }

    if (message.trim().length < 5) {
        alert('Please write at least 5 characters for your claim reason.');
        return;
    }

    const formData = new FormData();
    formData.append('action', 'claim');
    formData.append('item_id', itemId);
    formData.append('message', message.trim());

    fetch('../Controller/ajax.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => alert(data.message))
        .catch(() => alert('Could not send claim request.'));
}






document.addEventListener('DOMContentLoaded', function () {

    // LOGIN VAL
    const loginForm = document.querySelector('form[action*="Controller/login.php"]');

    if (loginForm) {
        loginForm.addEventListener('submit', function (event) {
            const studentId = loginForm.querySelector('[name="student_id"]');
            const password = loginForm.querySelector('[name="password"]');

            if (!studentId || !password) {
                return;
            }

            const idPattern = /^\d{2}-\d{5}-\d$/;

            if (studentId.value.trim() === '') {
                alert('Please enter your University ID.');
                event.preventDefault();
                studentId.focus();
                return;
            }

            if (!idPattern.test(studentId.value.trim())) {
                alert('University ID must look like 22-46183-1.');
                event.preventDefault();
                studentId.focus();
                return;
            }

            if (password.value.trim() === '') {
                alert('Please enter your password.');
                event.preventDefault();
                password.focus();
            }
        });
    }


    // REGISTRATION VAL
    const registrationForm = document.querySelector('form[action*="Controller/register.php"]');

    if (registrationForm) {
        registrationForm.addEventListener('submit', function (event) {
            const studentId = registrationForm.querySelector('[name="student_id"]');
            const name = registrationForm.querySelector('[name="name"]');
            const email = registrationForm.querySelector('[name="email"]');
            const password = registrationForm.querySelector('[name="password"]');
            const role = registrationForm.querySelector('[name="role"]');

            const idPattern = /^\d{2}-\d{5}-\d$/;
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (studentId.value.trim() === '') {
                alert('Please enter your University ID.');
                event.preventDefault();
                studentId.focus();
                return;
            }

            if (!idPattern.test(studentId.value.trim())) {
                alert('University ID must look like 22-46183-1.');
                event.preventDefault();
                studentId.focus();
                return;
            }

            if (name.value.trim() === '') {
                alert('Please enter your full name.');
                event.preventDefault();
                name.focus();
                return;
            }

            if (name.value.trim().length < 2) {
                alert('Name must contain at least 2 characters.');
                event.preventDefault();
                name.focus();
                return;
            }

            if (email.value.trim() === '') {
                alert('Please enter your email.');
                event.preventDefault();
                email.focus();
                return;
            }

            if (!emailPattern.test(email.value.trim())) {
                alert('Please enter a valid email address.');
                event.preventDefault();
                email.focus();
                return;
            }

            if (password.value === '') {
                alert('Please enter a password.');
                event.preventDefault();
                password.focus();
                return;
            }

            if (password.value.length < 6) {
                alert('Password must contain at least 6 characters.');
                event.preventDefault();
                password.focus();
                return;
            }

            if (role && role.value === '') {
                alert('Please select a user type.');
                event.preventDefault();
                role.focus();
            }
        });
    }


    // PROFILE VAL
    const profileForm = document.querySelector('form[action*="Controller/profile.php"]');

    if (profileForm) {
        profileForm.addEventListener('submit', function (event) {
            const name = profileForm.querySelector('[name="name"]');
            const email = profileForm.querySelector('[name="email"]');
            const password = profileForm.querySelector('[name="password"]');

            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (name.value.trim() === '') {
                alert('Please enter your name.');
                event.preventDefault();
                name.focus();
                return;
            }

            if (email.value.trim() === '') {
                alert('Please enter your email.');
                event.preventDefault();
                email.focus();
                return;
            }

            if (!emailPattern.test(email.value.trim())) {
                alert('Please enter a valid email address.');
                event.preventDefault();
                email.focus();
                return;
            }

            if (password.value !== '' && password.value.length < 6) {
                alert('New password must contain at least 6 characters.');
                event.preventDefault();
                password.focus();
            }
        });
    }


    // LOST & FOUND ITEM VAL
    const itemForm = document.querySelector('form[action*="Controller/addItem.php"]');

    if (itemForm) {
        itemForm.addEventListener('submit', function (event) {
            const itemName = itemForm.querySelector('[name="item_name"]');
            const category = itemForm.querySelector('[name="category"]');
            const description = itemForm.querySelector('[name="description"]');
            const location = itemForm.querySelector('[name="location"]');
            const itemDate = itemForm.querySelector('[name="item_date"]');

            if (itemName.value.trim() === '') {
                alert('Please enter the item name.');
                event.preventDefault();
                itemName.focus();
                return;
            }

            if (itemName.value.trim().length < 2) {
                alert('Item name must contain at least 2 characters.');
                event.preventDefault();
                itemName.focus();
                return;
            }

            if (category.value === '') {
                alert('Please select an item category.');
                event.preventDefault();
                category.focus();
                return;
            }

            if (description.value.trim() === '') {
                alert('Please enter an item description.');
                event.preventDefault();
                description.focus();
                return;
            }

            if (description.value.trim().length < 5) {
                alert('Description must contain at least 5 characters.');
                event.preventDefault();
                description.focus();
                return;
            }

            if (location.value.trim() === '') {
                alert('Please enter the campus location.');
                event.preventDefault();
                location.focus();
                return;
            }

            if (itemDate.value === '') {
                alert('Please select the date.');
                event.preventDefault();
                itemDate.focus();
            }
        });
    }


    // DELETE CONFIRM
  
    const deleteForms = document.querySelectorAll('form[action*="Controller/adminAction.php"]');

    deleteForms.forEach(function (form) {
        const action = form.querySelector('[name="action"]');

        if (action && action.value === 'delete_item') {
            form.addEventListener('submit', function (event) {
                if (!confirm('Delete this report?')) {
                    event.preventDefault();
                }
            });
        }
    });

});




function escapeHtml(text) {
    return String(text).replace(/[&<>'"]/g, function (character) {
        return {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            "'": '&#039;',
            '"': '&quot;'
        }[character];
    });
}
