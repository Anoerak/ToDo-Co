import React, { useState, useEffect } from 'react';
import DataTable from 'react-data-table-component';
import { Button } from '@mui/material';

// import './_DataTable.scss';

const UsersList = (prop) => {
	console.log('Utilisateur connecté :', prop.users);

	const [error, setError] = useState(null);
	const [isError, setIsError] = useState(false);
	const [datas, setDatas] = useState([]);
	const [dataTableTitle, setDataTableTitle] = useState('Liste');
	const [selectedRows, setSelectedRows] = React.useState([]);
	const [toggleCleared, setToggleCleared] = React.useState(false);

	const handleRowSelected = React.useCallback((state) => {
		setSelectedRows(state.selectedRows);
	}, []);

	const contextActions = React.useMemo(() => {
		const handleDelete = () => {
			if (prop.users) {
				if (
					window.confirm(`Êtes-vous certain de vouloir supprimer :\r ${selectedRows.map((r) => r.username)}?`)
				) {
					setToggleCleared(!toggleCleared);
					// We delete the selected rows
					selectedRows.map((row) => {
						// We check if the user is not the current user
						if (row.id !== prop.userId) {
							// We delete the user navigating to the delete route
							window.location.href = '/users/' + row.id + '/delete';
							// We wait 3 second and confirm the deletion
							setTimeout(() => {
								window.alert('Utilisateur supprimé avec succès');
								setIsError(false);
								setError('Utilisateur supprimé avec succès');
							}, 3000);
						} else {
							// We display an error message
							setIsError(true);
							setError('Vous ne pouvez pas supprimer votre propre compte');
						}
					});
				}
			} else {
				if (window.confirm(`Êtes-vous certain de vouloir supprimer :\r ${selectedRows.map((r) => r.title)}?`)) {
					setToggleCleared(!toggleCleared);
					// We delete the selected rows
					selectedRows.map((row) => {
						window.location.href = '/tasks/' + row.id + '/delete';
						// We wait 3 second and confirm the deletion
						setTimeout(() => {
							window.alert('Tâche supprimée avec succès');
							setIsError(false);
							setError('Tâche supprimés avec succès');
							// We redirect to the admin page
						}, 3000);
					});
				}
			}
		};
		return (
			<Button key='delete' onClick={handleDelete} style={{ backgroundColor: 'red', color: 'white' }}>
				Delete
			</Button>
		);
	}, [selectedRows, prop.users, prop.tasks, toggleCleared]);

	useEffect(() => {
		setDatas(prop.users ? prop.users : prop.tasks);
		setDataTableTitle(prop.users ? 'Liste des Utilisateurs' : 'Liste des Tâches');
		// We check if the Users DataTable is empty
		if (prop.users && prop.users.length < 2) {
			setIsError(true);
			setError('Aucun utilisateur trouvé');
		} else if (prop.tasks && prop.tasks.length < 1) {
			setIsError(true);
			setError('Aucune tâche trouvée');
		}
	}, [prop]);

	// Columns for the DataTable component
	const columns = prop.users
		? [
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
							<a href={row.edit} className='admin__button'>
								Modifier
							</a>
						</div>
					),
				},
		  ]
		: [
				{
					name: 'ID',
					selector: (row) => row.id,
					sortable: true,
				},
				{
					name: 'Titre',
					selector: (row) => row.title,
					sortable: true,
				},
				{
					name: 'Description',
					selector: (row) => row.content,
					sortable: true,
				},
				{
					name: 'Auteur',
					selector: (row) => row.author,
					sortable: true,
				},
				{
					name: 'En cours / Terminée',
					selector: (row) => (row.isDone === true ? 'Terminée' : 'En cours'),
					sortable: true,
				},
				{
					name: 'Actions',
					cell: (row) => (
						<div className='actions'>
							<a href={row.edit} className='admin__button'>
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
						title={dataTableTitle}
						columns={columns}
						data={datas}
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
