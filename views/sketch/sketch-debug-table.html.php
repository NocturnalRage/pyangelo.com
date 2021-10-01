    <!-- debug Table -->
    <div id="debugTableWrapper" class="row">
      <div class="col-md-12">
        <p class="appHeading">Debug Information</p>
        <p id="debugButtons" class="appHeading">
          <button id="stepInto" class="btn btn-info">Step Into</button>
          <button id="stepOver" class="btn btn-warning">Step Over</button>
          <button id="slowMotion" class="btn btn-danger">Slow Motion</button>
          <button id="continue" class="btn btn-success">Continue</button>
        </p>
        <table id="debugTable" class="table table-striped table-hover">
          <thead>
            <tr>
              <th>Scope</th>
              <th>Variable</th>
              <th>Value</th>
              <th>Datatype</th>
            </tr>
          </thead>
          <tbody id="debugTableBody">
          </tbody>
        </table>
      </div><!-- col-md-12 -->
    </div><!-- debugTableWrapper row -->

