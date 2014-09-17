<?php
/*
 * ��������� ��������
 * �������� ����� apply(), ������� ������ ��� ��������� �������
 */
interface Soulex_File_HttpUpload_Filter_Interface
{
	/**
	 *  
	 * @param object $filter
	 * @param object $params[optional]
	 * � �������� ���������� ����������� ������������
	 * uploaded_file_fullpath, uploaded_file_orig_name, uploaded_file_mime, uploaded_file_size
	 * ��� ����� �������� ������������������. 
	 */
	public function apply($filter, $params = null);
}

?>