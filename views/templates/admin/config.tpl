<p>Ce module permet de mettre à jour l'image utilisée comme fond du header.</p>

<div class="alert alert-info" style="margin-top: 20px;">
	Taille minimale recommandée de l'image: 200 pixels de haut et 1800 pixels de large.
</div>

{$form}

{if $result}
	<div class="alert alert-{$result.messageType}" style="margin-top: 20px;">{$result.message}</div>
{/if}
