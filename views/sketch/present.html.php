<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/header.html.php';
?>
  <div class="container">

<?php
include 'sketch-output.html.php';
?>

  </div><!-- container -->
<script type="text/python3" id="startSketch">
    import pyangelo
    import json
    from browser import ajax

    def onCompleteStartSketch(request):
      if request.status == 200 or request.status == 0:
        data = json.loads(request.text)
        pyangelo.startSketch(data["files"][0]["sourceCode"])
      else:
        print("error " + request.text)

    def getSourceCode():
      request = ajax.Ajax()
      request.bind('complete', onCompleteStartSketch)
      request.open('GET','/sketch/code/<?= $sketch['sketch_id'] ?>', True)
      request.set_header('accept', 'application/json')
      request.send()

    getSourceCode()
</script>

<script>
    function writeOutput(data, append) {
        console.log(data);
    }
</script>

</body>
</html>
