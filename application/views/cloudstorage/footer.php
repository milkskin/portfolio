<script>
$(function () {
	$("#jstree_demo_div")
	.jstree({
		"core" : {
			"data" : <?php echo $dir_list_json . PHP_EOL; ?>
		},
		"plugins" : [
			"types"
		],
		"types" : {
			"#" : {
				"valid_children" : ["root"]
			},
			"root" : {
				"valid_children" : ["default"]
			},
			"default" : {
				"valid_children" : ["default"]
			},
			"temporary" : {
				"valid_children" : []
			}
		}
	})
})
</script>
