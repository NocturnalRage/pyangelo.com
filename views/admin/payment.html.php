                <tr>
                  <td>
                    <?= $this->esc($payment['paid_at_formatted']) ?>
                  </td>
                  <td>
                    <?= $numberFormatter->formatCurrency($payment['display_amount'], $payment['currency_code']) ?>
                  </td>
                  <td>
                    <?= $this->esc($payment['payment_type_name']) ?>
                  </td>
                  <td>
                    <?php if ($payment['payment_type_name'] == 'Refund') : ?>
                    <?php elseif ($payment['refund_status'] ?? 'not_refunded' == 'FULL') : ?>
                      Refunded
                    <?php else : ?>
                      <form action="/admin/refund-subscription-payment" method="POST">
                        <input type="hidden" name="crsfToken" value="<?= $personInfo['crsfToken']; ?>" />
                        <input type="hidden" name="personId" value="<?= $this->esc($person['person_id']) ?>" />
                        <input type="hidden" name="chargeId" value="<?= $this->esc($payment['charge_id']) ?>" />
                        <input type="hidden" name="refundAmount" value="<?= $this->esc($payment['total_amount_in_cents']) ?>" />
                        <button type="submit" class="btn btn-warning" onclick="return confirm('Are you sure you want to refund this payment?')">
                        <i class="fa fa-refresh" aria-hidden="true"></i> Refund
                          <?= $numberFormatter->formatCurrency($payment['display_amount'], $payment['currency_code']) ?>
                        </button>
                      </form>
                    <?php endif; ?> 
                  </td>
                </tr>
