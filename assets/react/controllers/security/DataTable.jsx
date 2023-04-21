import React, { useState, useEffect } from 'react';
import DataTable from 'react-data-table-component';
import { Button } from '@mui/material';

const UsersList = (Users, User) => {
	console.log('Utilisateurs :', Users);

	// UseState to manage Error
	const [error, setError] = useState(null);
	const [isError, setIsError] = useState(false);
	const [selectedRows, setSelectedRows] = React.useState([]);
	const [toggleCleared, setToggleCleared] = React.useState(false);

	const handleRowSelected = React.useCallback((state) => {
		setSelectedRows(state.selectedRows);
	}, []);

	const contextActions = React.useMemo(() => {
		const handleEdit = () => {
			if (selectedRows.length === 1) {
				window.alert(`You want to edit:\r ${selectedRows.map((r) => r.title)}`);
			} else {
				window.alert('Please select a single row for edit');
			}
		};
		const handleDelete = () => {
			if (window.confirm(`Are you sure you want to delete:\r ${selectedRows.map((r) => r.username)}?`)) {
				setToggleCleared(!toggleCleared);
				// We delete the selected rows
				selectedRows.map((row) => {
					// We check if the user is not the current user
					if (row.id !== User.id) {
						// We delete the user navigating to the delete route
						window.location.href = '/users/' + row.id + '/delete';
						// We confirm the deletion
						setIsError(false);
						setError('Utilisateur supprimé avec succès');
					} else {
						// We display an error message
						setIsError(true);
						setError('Vous ne pouvez pas supprimer votre propre compte');
					}
				});
			}
		};

		return (
			<Button key='delete' onClick={handleDelete} style={{ backgroundColor: 'red', color: 'white' }}>
				Delete
			</Button>
		);
	}, [Users, selectedRows, toggleCleared]);

	useEffect(() => {
		// We check if the Users is empty
		if (Users.length === 0) {
			setIsError(true);
			setError('Aucun utilisateur trouvé');
		}
	}, [Users]);

	// Columns for the DataTable component
	const columns = [
		{
			name: 'ID',
			selector: (row) => row.id,
			sortable: true,
		},
		{
			name: 'Username',
			selector: (row) => row.username,
			sortable: true,
		},
		{
			name: 'Email',
			selector: (row) => row.email,
			sortable: true,
		},
		{
			name: 'Privilèges',
			selector: (row) => row.roles,
			sortable: true,
		},
		{
			name: 'Actions',
			cell: (row) => (
				<div className='actions'>
					<a href='' className='admin__button'>
						Modifier
					</a>
				</div>
			),
		},
	];

	return (
		<div className='usersList_container'>
			{/* We check if an error occurred, if so, we display it */}
			<div className='userList__tableContainer'>
				{isError ? (
					<div className='error-warning'>{error}</div>
				) : (
					<DataTable
						title='Liste des Utilisateurs'
						columns={columns}
						data={Users.Users}
						pagination
						paginationRowsPerPageOptions={[5, 10, 15, 20, 25, 30]}
						paginationPerPage={5}
						paginationComponentOptions={{
							rowsPerPageText: 'Rows per page:',
							rangeSeparatorText: 'of',
							noRowsPerPage: false,
							selectAllRowsItem: true,
							selectAllRowsItemText: 'All',
						}}
						selectableRows
						contextActions={contextActions}
						onSelectedRowsChange={handleRowSelected}
						clearSelectedRows={toggleCleared}
					/>
				)}
			</div>
		</div>
	);
};

export default UsersList;
