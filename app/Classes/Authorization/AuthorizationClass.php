<?php

namespace App\Classes\Authorization;

use Dlnsk\HierarchicalRBAC\Authorization;


/**
 *  This is example of hierarchical RBAC authorization configiration.
 */

class AuthorizationClass extends Authorization
{
	public function getPermissions() {
		return [
			'update-post' => [
                    'description' => 'Редактирование любых статей',
                ],
            'update-user' => [
                    'description' => 'Редактирование пользователей',
                ],   
			'editPost' => [
					'description' => 'Edit any posts',
					'next' => 'editOwnPost',           
				],
			'editColumns-1' => [
					'description' => 'Edit own columns',
				],
			'editColumns-2' => [
					'description' => 'Edit own columns',
				],
			'editColumns-3' => [
					'description' => 'Edit own columns',
				],
			'editColumns-4' => [
					'description' => 'Edit own columns',
				],
			'editDraft' => [
					'description' => 'Edit Draft',
				],
			'activateDraft' => [
					'description' => 'Activate Draft',
				],
			'view-post' => [
					'description' => 'View post',
				],
			'china-view-post' => [
					'description' => 'View post',
				],
			'china-update-post' => [
					'description' => 'Update post',
				],
			'eng-view-post' => [
					'description' => 'View post',
				],
			'eng-update-post' => [
					'description' => 'Update eng-post',
				],
			'editColumns-eng' => [
					'description' => 'Edit own columns',
				],
			'editColumns-eng-2' => [
					'description' => 'Edit own columns',
				],
			'editComments-eng' => [
					'description' => 'Edit Comments in eng-admin',
				],
			'editEngDraft' => [
					'description' => 'Edit Eng Draft',
				],
			'activateEngDraft' => [
					'description' => 'Activate Eng Draft',
				],
			'editOwnPost' => [
					'description' => 'Edit own post',
				],
			'editCourierTasks' => [
					'description' => 'Edit Courier Tasks',
				],
			'changeColor' => [
					'description' => 'Change color',
				]		
		];
	}

	public function getRoles() {
		return [
			'warehouse' => [
				'view-post',
				'update-post',
				'editColumns-2',
				'editColumns-3',
				'editColumns-eng-2',
				'eng-view-post',
				'editComments-eng',
				'eng-update-post',
				'editColumns-1'
			],
			'office_1' => [
				'view-post',
				'update-post',
				'editPost',
				'editColumns-2',
				'editColumns-eng-2',
				'editColumns-3',
				'china-update-post',
				'china-view-post',
				'eng-update-post',
				'editColumns-eng',
				'eng-view-post',
				'editComments-eng',
				'editColumns-1',
				'editColumns-4',
				'editDraft',
				'editEngDraft',
				'activateDraft',
				'activateEngDraft',
				'editCourierTasks',
				'changeColor'
			],
			'office_ru' => [
				'view-post',
				'update-post',
				'editColumns-1',
				'editDraft',
				'activateDraft',
				'editCourierTasks',
				'changeColor'					
			],
			'office_agent_ru' => [
				'view-post',
				'update-post',
				'editColumns-4',
				'editCourierTasks',
				'changeColor'				
			],
			'viewer' => [
				'view-post',
				'eng-view-post'
			],
			'viewer_1' => [
				'view-post'
			],
			'viewer_2' => [
				'view-post'
			],
			'viewer_3' => [
				'view-post'
			],
			'viewer_4' => [
				'view-post'
			],
			'viewer_5' => [
				'view-post'
			],
			'china_admin' => [
				'china-view-post',
				'china-update-post'
			],
			'china_viewer' => [
				'china-view-post',
			],
			'office_eng' => [
				'view-post',
				'eng-view-post',
				'eng-update-post',
				'editComments-eng',
				'editEngDraft',
				'activateEngDraft',
				'editCourierTasks',
				'changeColor'					
			],
			'office_ind' => [
				'view-post',
				'eng-view-post',
				'eng-update-post',
				'editColumns-eng',
				'editCourierTasks',
				'changeColor'					
			],
			'courier' => [
				'view-post',
				'eng-view-post',
				'editCourierTasks'
			],
			'viewer_eng' => [
				'eng-view-post',
			],			
			'user' => [
				'editOwnPost',
			],
		];
	}


	/**
	 * Methods which checking permissions.
	 * Methods should be present only if additional checking needs.
	 */

	public function editOwnPost($user, $post) {
		$post = $this->getModel(\App\Post::class, $post);  // helper method for geting model

		return $user->id === $post->user_id;
	}

}
