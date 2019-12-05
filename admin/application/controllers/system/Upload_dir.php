<?php
/**
 * author    mengxianghan
 * links     http://www.xuanyunet.com
 * date      2019/7/25
 */

use Ramsey\Uuid\Uuid;

class Upload_dir extends MY_Controller
{

    /**
     * 获取列表
     */
    public function get_list()
    {
        try {
            $status = $this->input->get('status');
            $where = '';
            if ($status != '') {
                $where = "ud.status = $status";
            }
            $result = $this->common->get_list(array(
                'table' => 'upload_dir',
                'where' => $where,
                'order_by' => 'sort asc,create_time asc',
                'has_pagination' => '0',
            ));
            $data = array(
                'list' => list_to_tree($result['list'])
            );
            return ajax(EXIT_SUCCESS, null, $data);
        } catch (Exception $e) {
            return ajax(EXIT_ERROR, $e->getMessage());
        }
    }

    /**
     * 提交
     */
    public function submit()
    {
        try {
            $id = $this->input->post('id');
            $name = $this->input->post('name');
            $values = array(
                'parent_id' => $this->input->post('parent_id'),
                'name' => $name,
                'status' => $this->input->post('status'),
                'sort' => $this->input->post('sort'),
            );
            if ($id) {
                $result = $this->common->update('upload_dir', array('id' => $id), $values);
            } else {
                $values['id'] = Uuid::uuid4();
                $result = $this->common->insert('upload_dir', $values);
            }
            return ajax(EXIT_SUCCESS, '保存成功', $result);
        } catch (Exception $e) {
            return ajax(EXIT_ERROR, $e->getMessage());
        }
    }

    /**
     * 删除
     */
    public function delete()
    {
        try {
            $id = $this->input->post('id');
            if ($id == '') {
                throw new Exception('参数不完整');
            }
            //检查是否含有下级
            $count = $this->common->count_all_results('upload_dir', "parent_id = $id");
            if ($count > 0) { //存在下级，禁止删除
                throw new Exception('存在下级数据，请勿删除！');
            }
            $result = $this->common->delete('upload_dir', array('id' => $id));
            return ajax(EXIT_SUCCESS, null, $result);
        } catch (Exception $e) {
            return ajax(EXIT_ERROR, $e->getMessage());
        }
    }
}
