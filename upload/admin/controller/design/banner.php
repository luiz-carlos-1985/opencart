<?php
namespace Opencart\Admin\Controller\Design;
class Banner extends \Opencart\System\Engine\Controller {
	public function index(): void {
		$this->load->language('design/banner');

		$this->document->setTitle($this->language->get('heading_title'));

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('design/banner', 'user_token=' . $this->session->data['user_token'] . $url)
		];

		$data['add'] = $this->url->link('design/banner|form', 'user_token=' . $this->session->data['user_token'] . $url);

		$data['user_token'] = $this->session->data['user_token'];

		$data['list'] = $this->getList();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('design/banner', $data));
	}

	public function list(): void {
		$this->response->setOutput($this->getList());
	}

	protected function getList(): string {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'name';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['banners'] = [];

		$filter_data = [
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_pagination_admin'),
			'limit' => $this->config->get('config_pagination_admin')
		];

		$this->load->model('design/banner');

		$banner_total = $this->model_design_banner->getTotalBanners();

		$results = $this->model_design_banner->getBanners($filter_data);

		foreach ($results as $result) {
			$data['banners'][] = [
				'banner_id' => $result['banner_id'],
				'name'      => $result['name'],
				'status'    => ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
				'edit'      => $this->url->link('design/banner|edit', 'user_token=' . $this->session->data['user_token'] . '&banner_id=' . $result['banner_id'] . $url)
			];
		}

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('design/banner', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url);
		$data['sort_status'] = $this->url->link('design/banner', 'user_token=' . $this->session->data['user_token'] . '&sort=status' . $url);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $banner_total,
			'page'  => $page,
			'limit' => $this->config->get('config_pagination_admin'),
			'url'   => $this->url->link('design/banner', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}')
		]);

		$data['results'] = sprintf($this->language->get('text_pagination'), ($banner_total) ? (($page - 1) * $this->config->get('config_pagination_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_pagination_admin')) > ($banner_total - $this->config->get('config_pagination_admin'))) ? $banner_total : ((($page - 1) * $this->config->get('config_pagination_admin')) + $this->config->get('config_pagination_admin')), $banner_total, ceil($banner_total / $this->config->get('config_pagination_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		return $this->load->view('design/banner_list', $data);
	}

	protected function form(): void {
		$data['text_form'] = !isset($this->request->get['banner_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('design/banner', 'user_token=' . $this->session->data['user_token'] . $url)
		];

		if (!isset($this->request->get['banner_id'])) {
			$data['action'] = $this->url->link('design/banner|add', 'user_token=' . $this->session->data['user_token'] . $url);
		} else {
			$data['action'] = $this->url->link('design/banner|edit', 'user_token=' . $this->session->data['user_token'] . '&banner_id=' . $this->request->get['banner_id'] . $url);
		}

		$data['back'] = $this->url->link('design/banner', 'user_token=' . $this->session->data['user_token'] . $url);

		if (isset($this->request->get['banner_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$banner_info = $this->model_design_banner->getBanner($this->request->get['banner_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];

		if (!empty($banner_info)) {
			$data['name'] = $banner_info['name'];
		} else {
			$data['name'] = '';
		}

		if (!empty($banner_info)) {
			$data['status'] = $banner_info['status'];
		} else {
			$data['status'] = true;
		}

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		$this->load->model('tool/image');

		if (!empty($banner_info)) {
			$banner_images = $this->model_design_banner->getImages($this->request->get['banner_id']);
		} else {
			$banner_images = [];
		}

		$data['banner_images'] = [];

		foreach ($banner_images as $language_id => $banner_image) {
			foreach ($banner_image as $value) {
				if (is_file(DIR_IMAGE . html_entity_decode($value['image'], ENT_QUOTES, 'UTF-8'))) {
					$image = $value['image'];
					$thumb = $value['image'];
				} else {
					$image = '';
					$thumb = 'no_image.png';
				}
				
				$data['banner_images'][$language_id][] = [
					'title'      => $value['title'],
					'link'       => $value['link'],
					'image'      => $image,
					'thumb'      => $this->model_tool_image->resize(html_entity_decode($thumb, ENT_QUOTES, 'UTF-8'), 100, 100),
					'sort_order' => $value['sort_order']
				];
			}
		}

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('design/banner_form', $data));
	}

	public function save(): void {
		$this->load->language('design/banner');

		$json = [];

		if (!$this->user->hasPermission('modify', 'design/banner')) {
			$json['error']['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen(trim($this->request->post['name'])) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
			$json['error']['name'] = $this->language->get('error_name');
		}

		if (isset($this->request->post['banner_image'])) {
			foreach ($this->request->post['banner_image'] as $language_id => $banner_image) {
				foreach ($banner_image as $key => $value) {
					if ((utf8_strlen(trim($value['title'])) < 2) || (utf8_strlen($value['title']) > 64)) {
						$json['error']['banner_image'][$language_id][$key] = $this->language->get('error_title');
					}
				}
			}
		}

		if (!$json) {
			$this->load->model('design/banner');

			if (!isset($this->request->get['banner_id'])) {
				$this->model_design_banner->addBanner($this->request->post);
			} else {
				$this->model_design_banner->editBanner($this->request->get['banner_id'], $this->request->post);
			}

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function delete(): void {
		$this->load->language('design/banner');

		$json = [];

		if (isset($this->request->post['selected'])) {
			$selected = $this->request->post['selected'];
		} else {
			$selected = [];
		}

		if (!$this->user->hasPermission('modify', 'design/banner')) {
			$json['error'] = $this->language->get('error_permission');
		}

		if (!$json) {
			$this->load->model('design/banner');

			foreach ($selected as $banner_id) {
				$this->model_design_banner->deleteBanner($banner_id);
			}

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
