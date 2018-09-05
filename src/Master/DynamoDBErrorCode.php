<?php
namespace konnosena\DynamoDB\Master;


class DynamoDBErrorCode
{
	/**
	 * アクセスが拒否されました。
	 * クライアントがリクエストに正しく署名しませんでした。AWS SDKを使用する場合、リクエストは自動的に署名されます。
	 */
	const ACCESS_DENIED_EXCEPTION = "AccessDeniedException";

	/**
	 * 条件付きリクエストが失敗しました。
	 * false と評価された条件を指定しました。 たとえば、項目に条件付き更新を実行しようとしたかもしれませんが、属性の実際の値は、条件の予期される値と一致しませんでした。
	 */
	const CONDITIONAL_CHECK_FAILED_EXCEPTION = "ConditionalCheckFailedException";

	/**
	 * リクエストの署名が AWS 基準に適合しません。
	 */
	const INCOMPLETE_SIGNATURE_EXCEPTION = "IncompleteSignatureException";

	/**
	 * コレクションサイズが超過しました。
	 * local secondary indexがあるテーブルの場合、同じパーティションキー値を持つ項目のグループが、10 GB の最大サイズ制限を超過しました。
	 * Retry: ok
	 */
	const ITEM_COLLECTION_SIZE_LIMIT_EXCEEDED_EXCEPTION = "ItemCollectionSizeLimitExceededException";

	/**
	 * 特定のサブスクライバに対するオペレーションが多すぎます。
	 * 同時オペレーションのコントロールプレーンが多すぎます。 CREATING、DELETINGまたはUPDATINGの状態のテーブルやインデックスの累積数が、10 を超えることはできません。
	 * Retry: ok
	 */
	const LIMIT_EXCEEDED_EXCEPTION = "LimitExceededException";

	/**
	 * リクエストには、有効な（登録済みの）AWS Access Key ID が含まれている必要があります。
	 */
	const MISSING_AUTHENTICATION_TOKEN_EXCEPTION = "MissingAuthenticationTokenException";

	/**
	 * 1 つのテーブルまたは 1 つ以上のグローバルセカンダリインデックスのプロビジョンドスループットが許容されている最大値を超えました。
	 * Retry: OK
	 */
	const PROVISIONED_THROUGHPUT_EXCEEDED_EXCEPTION = "ProvisionedThroughputExceededException";

	/**
	 * 変更しようとしているリソースは使用中です。
	 * 既存のテーブルを再作成しようとしたか、CREATING 状態にあるテーブルを削除しようとしました。
	 */
	const RESOURCE_IN_USE_EXCEPTION = "ResourceInUseException";

	/**
	 * リクエストされたリソースは存在しません。
	 * リクエストされたテーブルが存在しないか、ごく初期の CREATING 状態にあります。
	 */
	const RESOURCE_NOT_FOUND_EXCEPTION = "ResourceNotFoundException";

	/**
	 * リクエストの速度が、許容されているスループットを超えています。
	 * CreateTable、UpdateTable、DeleteTable オペレーションの実行が速すぎる場合、次の例外が返されることがあります。
	 * Retry: OK
	 */
	const THROTTLING_EXCEPTION = "ThrottlingException";

	/**
	 * アクセスキー ID またはセキュリティトークンが無効です。
	 * リクエスト署名が間違っています。最も可能性の高い原因は、AWS アクセスキー ID またはシークレットキーが無効であることです。
	 * Retry: OK
	 */
	const UNRECOGNIZED_CLIENT_EXCEPTION = "UnrecognizedClientException";

	/**
	 * このエラーは、必須パラメータが指定されていない、値が範囲外である、データ型が一致しない、などいくつかの理由で発生します。
	 * エラーメッセージに、エラーを引き起こしたリクエストの特定部分に関する詳細が含まれています。
	 */
	const VALIDATION_EXCEPTION = "ValidationException";
	
	/**
	 * サーバ内部エラー
	 */
	const INTERNAL_SERVER_ERROR = "InternalServerError";
}