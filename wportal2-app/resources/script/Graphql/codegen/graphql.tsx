import { gql } from '@apollo/client';
import * as Apollo from '@apollo/client';
export type Maybe<T> = T | null;
export type InputMaybe<T> = Maybe<T>;
export type Exact<T extends { [key: string]: unknown }> = {
  [K in keyof T]: T[K];
};
export type MakeOptional<T, K extends keyof T> = Omit<T, K> & {
  [SubKey in K]?: Maybe<T[SubKey]>;
};
export type MakeMaybe<T, K extends keyof T> = Omit<T, K> & {
  [SubKey in K]: Maybe<T[SubKey]>;
};
export type MakeEmpty<
  T extends { [key: string]: unknown },
  K extends keyof T,
> = { [_ in K]?: never };
export type Incremental<T> =
  | T
  | {
      [P in keyof T]?: P extends ' $fragmentName' | '__typename' ? T[P] : never;
    };
const defaultOptions = {} as const;
/** All built-in and custom scalars, mapped to their actual values */
export type Scalars = {
  ID: { input: string; output: string };
  String: { input: string; output: string };
  Boolean: { input: boolean; output: boolean };
  Int: { input: number; output: number };
  Float: { input: number; output: number };
  /** A datetime string with format `Y-m-d H:i:s`, e.g. `2018-05-23 13:43:32`. */
  DateTime: { input: any; output: any };
  /** Can be used as an argument to upload files using https://github.com/jaydenseric/graphql-multipart-request-spec */
  Upload: { input: any; output: any };
};

export type AppPersonalSettingSpPasswordResponse = {
  __typename?: 'AppPersonalSettingSpPasswordResponse';
  errors?: Maybe<Scalars['String']['output']>;
  statusCode: Scalars['Int']['output'];
  statusMessage: Scalars['String']['output'];
  url: Scalars['String']['output'];
};

export type AppTrnDivision = {
  __typename?: 'AppTrnDivision';
  id: Scalars['Int']['output'];
  name?: Maybe<Scalars['String']['output']>;
};

export type AppTrnProject = {
  __typename?: 'AppTrnProject';
  id: Scalars['Int']['output'];
  name?: Maybe<Scalars['String']['output']>;
};

export type AppTrnUser = {
  __typename?: 'AppTrnUser';
  authId: Scalars['Int']['output'];
  faceImagePath?: Maybe<Scalars['String']['output']>;
  id: Scalars['Int']['output'];
  nickname?: Maybe<Scalars['String']['output']>;
};

export type Mutation = {
  __typename?: 'Mutation';
  AppPersonalSettingShowAlignSlack?: Maybe<Response>;
  AppPersonalSettingSpConfirmCode?: Maybe<Response>;
  AppPersonalSettingSpPassword?: Maybe<AppPersonalSettingSpPasswordResponse>;
  SampleInputFormUpdate?: Maybe<Response>;
  SampleRedisUpdate?: Maybe<Response>;
};

export type MutationAppPersonalSettingSpConfirmCodeArgs = {
  code: Scalars['String']['input'];
};

export type MutationAppPersonalSettingSpPasswordArgs = {
  password: Scalars['String']['input'];
};

export type MutationSampleInputFormUpdateArgs = {
  inputMulti?: InputMaybe<Scalars['String']['input']>;
  inputSelectStringId?: InputMaybe<Scalars['Int']['input']>;
  inputSingleEmail?: InputMaybe<Scalars['String']['input']>;
  inputSingleNotZero?: InputMaybe<Scalars['String']['input']>;
  inputSingleNumber?: InputMaybe<Scalars['String']['input']>;
  inputSingleRange?: InputMaybe<Scalars['String']['input']>;
};

export type MutationSampleRedisUpdateArgs = {
  storeValue?: InputMaybe<Scalars['String']['input']>;
};

export type Query = {
  __typename?: 'Query';
  AppTrnDivisionList: Array<Maybe<AppTrnDivision>>;
  AppTrnProjectList: Array<Maybe<AppTrnProject>>;
  AppTrnUserList: Array<Maybe<AppTrnUser>>;
  SamplePagination: SamplePagination;
  SampleRedis: SampleRedis;
  UsecasesCalendarUserScheduleList: Array<Maybe<UsecasesCalendarUserSchedule>>;
};

export type QuerySamplePaginationArgs = {
  pageIndex: Scalars['Int']['input'];
  pageStep: Scalars['Int']['input'];
};

export type QuerySampleRedisArgs = {
  sampleArgument: Scalars['String']['input'];
};

export type QueryUsecasesCalendarUserScheduleListArgs = {
  emailList: Array<Scalars['String']['input']>;
  endDate: Scalars['String']['input'];
  startDate: Scalars['String']['input'];
};

export type Response = {
  __typename?: 'Response';
  errors?: Maybe<Scalars['String']['output']>;
  statusCode: Scalars['Int']['output'];
  statusMessage: Scalars['String']['output'];
};

export type SamplePagination = {
  __typename?: 'SamplePagination';
  count: Scalars['Int']['output'];
  list: Array<Maybe<AppTrnUser>>;
};

export type SampleRedis = {
  __typename?: 'SampleRedis';
  storeValue: Scalars['String']['output'];
};

export type UsecasesCalendarUserSchedule = {
  __typename?: 'UsecasesCalendarUserSchedule';
  calendarId: Scalars['String']['output'];
  end: Scalars['String']['output'];
  eventId: Scalars['String']['output'];
  identify: Scalars['String']['output'];
  kind: Scalars['String']['output'];
  start: Scalars['String']['output'];
  summary: Scalars['String']['output'];
};

export type AppPersonalSettingShowAlignSlackMutationVariables = Exact<{
  [key: string]: never;
}>;

export type AppPersonalSettingShowAlignSlackMutation = {
  __typename?: 'Mutation';
  AppPersonalSettingShowAlignSlack?: {
    __typename?: 'Response';
    statusCode: number;
    statusMessage: string;
    errors?: string | null;
  } | null;
};

export type AppPersonalSettingSpPasswordMutationVariables = Exact<{
  password: Scalars['String']['input'];
}>;

export type AppPersonalSettingSpPasswordMutation = {
  __typename?: 'Mutation';
  AppPersonalSettingSpPassword?: {
    __typename?: 'AppPersonalSettingSpPasswordResponse';
    statusCode: number;
    statusMessage: string;
    errors?: string | null;
    url: string;
  } | null;
};

export type AppPersonalSettingSpConfirmCodeMutationVariables = Exact<{
  code: Scalars['String']['input'];
}>;

export type AppPersonalSettingSpConfirmCodeMutation = {
  __typename?: 'Mutation';
  AppPersonalSettingSpConfirmCode?: {
    __typename?: 'Response';
    statusCode: number;
    statusMessage: string;
    errors?: string | null;
  } | null;
};

export type AppTrnUserListQueryVariables = Exact<{ [key: string]: never }>;

export type AppTrnUserListQuery = {
  __typename?: 'Query';
  AppTrnUserList: Array<{
    __typename?: 'AppTrnUser';
    id: number;
    authId: number;
    nickname?: string | null;
    faceImagePath?: string | null;
  } | null>;
};

export type AppTrnDivisionListQueryVariables = Exact<{ [key: string]: never }>;

export type AppTrnDivisionListQuery = {
  __typename?: 'Query';
  AppTrnDivisionList: Array<{
    __typename?: 'AppTrnDivision';
    id: number;
    name?: string | null;
  } | null>;
};

export type AppTrnProjectListQueryVariables = Exact<{ [key: string]: never }>;

export type AppTrnProjectListQuery = {
  __typename?: 'Query';
  AppTrnProjectList: Array<{
    __typename?: 'AppTrnProject';
    id: number;
    name?: string | null;
  } | null>;
};

export type SampleInputFormUpdateMutationVariables = Exact<{
  inputSingleNumber?: InputMaybe<Scalars['String']['input']>;
  inputSingleNotZero?: InputMaybe<Scalars['String']['input']>;
  inputSingleRange?: InputMaybe<Scalars['String']['input']>;
  inputSingleEmail?: InputMaybe<Scalars['String']['input']>;
  inputMulti?: InputMaybe<Scalars['String']['input']>;
  inputSelectStringId?: InputMaybe<Scalars['Int']['input']>;
}>;

export type SampleInputFormUpdateMutation = {
  __typename?: 'Mutation';
  SampleInputFormUpdate?: {
    __typename?: 'Response';
    statusCode: number;
    statusMessage: string;
    errors?: string | null;
  } | null;
};

export type SampleRedisUpdateMutationVariables = Exact<{
  storeValue?: InputMaybe<Scalars['String']['input']>;
}>;

export type SampleRedisUpdateMutation = {
  __typename?: 'Mutation';
  SampleRedisUpdate?: {
    __typename?: 'Response';
    statusCode: number;
    statusMessage: string;
    errors?: string | null;
  } | null;
};

export type SamplePaginationQueryVariables = Exact<{
  pageIndex: Scalars['Int']['input'];
  pageStep: Scalars['Int']['input'];
}>;

export type SamplePaginationQuery = {
  __typename?: 'Query';
  SamplePagination: {
    __typename?: 'SamplePagination';
    count: number;
    list: Array<{
      __typename?: 'AppTrnUser';
      id: number;
      authId: number;
      nickname?: string | null;
      faceImagePath?: string | null;
    } | null>;
  };
};

export type SampleRedisQueryVariables = Exact<{
  sampleArgument: Scalars['String']['input'];
}>;

export type SampleRedisQuery = {
  __typename?: 'Query';
  SampleRedis: { __typename?: 'SampleRedis'; storeValue: string };
};

export type UsecasesCalendarUserScheduleListQueryVariables = Exact<{
  emailList: Array<Scalars['String']['input']> | Scalars['String']['input'];
  startDate: Scalars['String']['input'];
  endDate: Scalars['String']['input'];
}>;

export type UsecasesCalendarUserScheduleListQuery = {
  __typename?: 'Query';
  UsecasesCalendarUserScheduleList: Array<{
    __typename?: 'UsecasesCalendarUserSchedule';
    identify: string;
    eventId: string;
    calendarId: string;
    kind: string;
    summary: string;
    start: string;
    end: string;
  } | null>;
};

export const AppPersonalSettingShowAlignSlackDocument = gql`
  mutation AppPersonalSettingShowAlignSlack {
    AppPersonalSettingShowAlignSlack {
      statusCode
      statusMessage
      errors
    }
  }
`;
export type AppPersonalSettingShowAlignSlackMutationFn =
  Apollo.MutationFunction<
    AppPersonalSettingShowAlignSlackMutation,
    AppPersonalSettingShowAlignSlackMutationVariables
  >;

/**
 * __useAppPersonalSettingShowAlignSlackMutation__
 *
 * To run a mutation, you first call `useAppPersonalSettingShowAlignSlackMutation` within a React component and pass it any options that fit your needs.
 * When your component renders, `useAppPersonalSettingShowAlignSlackMutation` returns a tuple that includes:
 * - A mutate function that you can call at any time to execute the mutation
 * - An object with fields that represent the current status of the mutation's execution
 *
 * @param baseOptions options that will be passed into the mutation, supported options are listed on: https://www.apollographql.com/docs/react/api/react-hooks/#options-2;
 *
 * @example
 * const [appPersonalSettingShowAlignSlackMutation, { data, loading, error }] = useAppPersonalSettingShowAlignSlackMutation({
 *   variables: {
 *   },
 * });
 */
export function useAppPersonalSettingShowAlignSlackMutation(
  baseOptions?: Apollo.MutationHookOptions<
    AppPersonalSettingShowAlignSlackMutation,
    AppPersonalSettingShowAlignSlackMutationVariables
  >,
) {
  const options = { ...defaultOptions, ...baseOptions };
  return Apollo.useMutation<
    AppPersonalSettingShowAlignSlackMutation,
    AppPersonalSettingShowAlignSlackMutationVariables
  >(AppPersonalSettingShowAlignSlackDocument, options);
}
export type AppPersonalSettingShowAlignSlackMutationHookResult = ReturnType<
  typeof useAppPersonalSettingShowAlignSlackMutation
>;
export type AppPersonalSettingShowAlignSlackMutationResult =
  Apollo.MutationResult<AppPersonalSettingShowAlignSlackMutation>;
export type AppPersonalSettingShowAlignSlackMutationOptions =
  Apollo.BaseMutationOptions<
    AppPersonalSettingShowAlignSlackMutation,
    AppPersonalSettingShowAlignSlackMutationVariables
  >;
export const AppPersonalSettingSpPasswordDocument = gql`
  mutation AppPersonalSettingSpPassword($password: String!) {
    AppPersonalSettingSpPassword(password: $password) {
      statusCode
      statusMessage
      errors
      url
    }
  }
`;
export type AppPersonalSettingSpPasswordMutationFn = Apollo.MutationFunction<
  AppPersonalSettingSpPasswordMutation,
  AppPersonalSettingSpPasswordMutationVariables
>;

/**
 * __useAppPersonalSettingSpPasswordMutation__
 *
 * To run a mutation, you first call `useAppPersonalSettingSpPasswordMutation` within a React component and pass it any options that fit your needs.
 * When your component renders, `useAppPersonalSettingSpPasswordMutation` returns a tuple that includes:
 * - A mutate function that you can call at any time to execute the mutation
 * - An object with fields that represent the current status of the mutation's execution
 *
 * @param baseOptions options that will be passed into the mutation, supported options are listed on: https://www.apollographql.com/docs/react/api/react-hooks/#options-2;
 *
 * @example
 * const [appPersonalSettingSpPasswordMutation, { data, loading, error }] = useAppPersonalSettingSpPasswordMutation({
 *   variables: {
 *      password: // value for 'password'
 *   },
 * });
 */
export function useAppPersonalSettingSpPasswordMutation(
  baseOptions?: Apollo.MutationHookOptions<
    AppPersonalSettingSpPasswordMutation,
    AppPersonalSettingSpPasswordMutationVariables
  >,
) {
  const options = { ...defaultOptions, ...baseOptions };
  return Apollo.useMutation<
    AppPersonalSettingSpPasswordMutation,
    AppPersonalSettingSpPasswordMutationVariables
  >(AppPersonalSettingSpPasswordDocument, options);
}
export type AppPersonalSettingSpPasswordMutationHookResult = ReturnType<
  typeof useAppPersonalSettingSpPasswordMutation
>;
export type AppPersonalSettingSpPasswordMutationResult =
  Apollo.MutationResult<AppPersonalSettingSpPasswordMutation>;
export type AppPersonalSettingSpPasswordMutationOptions =
  Apollo.BaseMutationOptions<
    AppPersonalSettingSpPasswordMutation,
    AppPersonalSettingSpPasswordMutationVariables
  >;
export const AppPersonalSettingSpConfirmCodeDocument = gql`
  mutation AppPersonalSettingSpConfirmCode($code: String!) {
    AppPersonalSettingSpConfirmCode(code: $code) {
      statusCode
      statusMessage
      errors
    }
  }
`;
export type AppPersonalSettingSpConfirmCodeMutationFn = Apollo.MutationFunction<
  AppPersonalSettingSpConfirmCodeMutation,
  AppPersonalSettingSpConfirmCodeMutationVariables
>;

/**
 * __useAppPersonalSettingSpConfirmCodeMutation__
 *
 * To run a mutation, you first call `useAppPersonalSettingSpConfirmCodeMutation` within a React component and pass it any options that fit your needs.
 * When your component renders, `useAppPersonalSettingSpConfirmCodeMutation` returns a tuple that includes:
 * - A mutate function that you can call at any time to execute the mutation
 * - An object with fields that represent the current status of the mutation's execution
 *
 * @param baseOptions options that will be passed into the mutation, supported options are listed on: https://www.apollographql.com/docs/react/api/react-hooks/#options-2;
 *
 * @example
 * const [appPersonalSettingSpConfirmCodeMutation, { data, loading, error }] = useAppPersonalSettingSpConfirmCodeMutation({
 *   variables: {
 *      code: // value for 'code'
 *   },
 * });
 */
export function useAppPersonalSettingSpConfirmCodeMutation(
  baseOptions?: Apollo.MutationHookOptions<
    AppPersonalSettingSpConfirmCodeMutation,
    AppPersonalSettingSpConfirmCodeMutationVariables
  >,
) {
  const options = { ...defaultOptions, ...baseOptions };
  return Apollo.useMutation<
    AppPersonalSettingSpConfirmCodeMutation,
    AppPersonalSettingSpConfirmCodeMutationVariables
  >(AppPersonalSettingSpConfirmCodeDocument, options);
}
export type AppPersonalSettingSpConfirmCodeMutationHookResult = ReturnType<
  typeof useAppPersonalSettingSpConfirmCodeMutation
>;
export type AppPersonalSettingSpConfirmCodeMutationResult =
  Apollo.MutationResult<AppPersonalSettingSpConfirmCodeMutation>;
export type AppPersonalSettingSpConfirmCodeMutationOptions =
  Apollo.BaseMutationOptions<
    AppPersonalSettingSpConfirmCodeMutation,
    AppPersonalSettingSpConfirmCodeMutationVariables
  >;
export const AppTrnUserListDocument = gql`
  query AppTrnUserList {
    AppTrnUserList {
      id
      authId
      nickname
      faceImagePath
    }
  }
`;

/**
 * __useAppTrnUserListQuery__
 *
 * To run a query within a React component, call `useAppTrnUserListQuery` and pass it any options that fit your needs.
 * When your component renders, `useAppTrnUserListQuery` returns an object from Apollo Client that contains loading, error, and data properties
 * you can use to render your UI.
 *
 * @param baseOptions options that will be passed into the query, supported options are listed on: https://www.apollographql.com/docs/react/api/react-hooks/#options;
 *
 * @example
 * const { data, loading, error } = useAppTrnUserListQuery({
 *   variables: {
 *   },
 * });
 */
export function useAppTrnUserListQuery(
  baseOptions?: Apollo.QueryHookOptions<
    AppTrnUserListQuery,
    AppTrnUserListQueryVariables
  >,
) {
  const options = { ...defaultOptions, ...baseOptions };
  return Apollo.useQuery<AppTrnUserListQuery, AppTrnUserListQueryVariables>(
    AppTrnUserListDocument,
    options,
  );
}
export function useAppTrnUserListLazyQuery(
  baseOptions?: Apollo.LazyQueryHookOptions<
    AppTrnUserListQuery,
    AppTrnUserListQueryVariables
  >,
) {
  const options = { ...defaultOptions, ...baseOptions };
  return Apollo.useLazyQuery<AppTrnUserListQuery, AppTrnUserListQueryVariables>(
    AppTrnUserListDocument,
    options,
  );
}
// @ts-ignore
export function useAppTrnUserListSuspenseQuery(
  baseOptions?: Apollo.SuspenseQueryHookOptions<
    AppTrnUserListQuery,
    AppTrnUserListQueryVariables
  >,
): Apollo.UseSuspenseQueryResult<
  AppTrnUserListQuery,
  AppTrnUserListQueryVariables
>;
export function useAppTrnUserListSuspenseQuery(
  baseOptions?:
    | Apollo.SkipToken
    | Apollo.SuspenseQueryHookOptions<
        AppTrnUserListQuery,
        AppTrnUserListQueryVariables
      >,
): Apollo.UseSuspenseQueryResult<
  AppTrnUserListQuery | undefined,
  AppTrnUserListQueryVariables
>;
export function useAppTrnUserListSuspenseQuery(
  baseOptions?:
    | Apollo.SkipToken
    | Apollo.SuspenseQueryHookOptions<
        AppTrnUserListQuery,
        AppTrnUserListQueryVariables
      >,
) {
  const options =
    baseOptions === Apollo.skipToken
      ? baseOptions
      : { ...defaultOptions, ...baseOptions };
  return Apollo.useSuspenseQuery<
    AppTrnUserListQuery,
    AppTrnUserListQueryVariables
  >(AppTrnUserListDocument, options);
}
export type AppTrnUserListQueryHookResult = ReturnType<
  typeof useAppTrnUserListQuery
>;
export type AppTrnUserListLazyQueryHookResult = ReturnType<
  typeof useAppTrnUserListLazyQuery
>;
export type AppTrnUserListSuspenseQueryHookResult = ReturnType<
  typeof useAppTrnUserListSuspenseQuery
>;
export type AppTrnUserListQueryResult = Apollo.QueryResult<
  AppTrnUserListQuery,
  AppTrnUserListQueryVariables
>;
export const AppTrnDivisionListDocument = gql`
  query AppTrnDivisionList {
    AppTrnDivisionList {
      id
      name
    }
  }
`;

/**
 * __useAppTrnDivisionListQuery__
 *
 * To run a query within a React component, call `useAppTrnDivisionListQuery` and pass it any options that fit your needs.
 * When your component renders, `useAppTrnDivisionListQuery` returns an object from Apollo Client that contains loading, error, and data properties
 * you can use to render your UI.
 *
 * @param baseOptions options that will be passed into the query, supported options are listed on: https://www.apollographql.com/docs/react/api/react-hooks/#options;
 *
 * @example
 * const { data, loading, error } = useAppTrnDivisionListQuery({
 *   variables: {
 *   },
 * });
 */
export function useAppTrnDivisionListQuery(
  baseOptions?: Apollo.QueryHookOptions<
    AppTrnDivisionListQuery,
    AppTrnDivisionListQueryVariables
  >,
) {
  const options = { ...defaultOptions, ...baseOptions };
  return Apollo.useQuery<
    AppTrnDivisionListQuery,
    AppTrnDivisionListQueryVariables
  >(AppTrnDivisionListDocument, options);
}
export function useAppTrnDivisionListLazyQuery(
  baseOptions?: Apollo.LazyQueryHookOptions<
    AppTrnDivisionListQuery,
    AppTrnDivisionListQueryVariables
  >,
) {
  const options = { ...defaultOptions, ...baseOptions };
  return Apollo.useLazyQuery<
    AppTrnDivisionListQuery,
    AppTrnDivisionListQueryVariables
  >(AppTrnDivisionListDocument, options);
}
// @ts-ignore
export function useAppTrnDivisionListSuspenseQuery(
  baseOptions?: Apollo.SuspenseQueryHookOptions<
    AppTrnDivisionListQuery,
    AppTrnDivisionListQueryVariables
  >,
): Apollo.UseSuspenseQueryResult<
  AppTrnDivisionListQuery,
  AppTrnDivisionListQueryVariables
>;
export function useAppTrnDivisionListSuspenseQuery(
  baseOptions?:
    | Apollo.SkipToken
    | Apollo.SuspenseQueryHookOptions<
        AppTrnDivisionListQuery,
        AppTrnDivisionListQueryVariables
      >,
): Apollo.UseSuspenseQueryResult<
  AppTrnDivisionListQuery | undefined,
  AppTrnDivisionListQueryVariables
>;
export function useAppTrnDivisionListSuspenseQuery(
  baseOptions?:
    | Apollo.SkipToken
    | Apollo.SuspenseQueryHookOptions<
        AppTrnDivisionListQuery,
        AppTrnDivisionListQueryVariables
      >,
) {
  const options =
    baseOptions === Apollo.skipToken
      ? baseOptions
      : { ...defaultOptions, ...baseOptions };
  return Apollo.useSuspenseQuery<
    AppTrnDivisionListQuery,
    AppTrnDivisionListQueryVariables
  >(AppTrnDivisionListDocument, options);
}
export type AppTrnDivisionListQueryHookResult = ReturnType<
  typeof useAppTrnDivisionListQuery
>;
export type AppTrnDivisionListLazyQueryHookResult = ReturnType<
  typeof useAppTrnDivisionListLazyQuery
>;
export type AppTrnDivisionListSuspenseQueryHookResult = ReturnType<
  typeof useAppTrnDivisionListSuspenseQuery
>;
export type AppTrnDivisionListQueryResult = Apollo.QueryResult<
  AppTrnDivisionListQuery,
  AppTrnDivisionListQueryVariables
>;
export const AppTrnProjectListDocument = gql`
  query AppTrnProjectList {
    AppTrnProjectList {
      id
      name
    }
  }
`;

/**
 * __useAppTrnProjectListQuery__
 *
 * To run a query within a React component, call `useAppTrnProjectListQuery` and pass it any options that fit your needs.
 * When your component renders, `useAppTrnProjectListQuery` returns an object from Apollo Client that contains loading, error, and data properties
 * you can use to render your UI.
 *
 * @param baseOptions options that will be passed into the query, supported options are listed on: https://www.apollographql.com/docs/react/api/react-hooks/#options;
 *
 * @example
 * const { data, loading, error } = useAppTrnProjectListQuery({
 *   variables: {
 *   },
 * });
 */
export function useAppTrnProjectListQuery(
  baseOptions?: Apollo.QueryHookOptions<
    AppTrnProjectListQuery,
    AppTrnProjectListQueryVariables
  >,
) {
  const options = { ...defaultOptions, ...baseOptions };
  return Apollo.useQuery<
    AppTrnProjectListQuery,
    AppTrnProjectListQueryVariables
  >(AppTrnProjectListDocument, options);
}
export function useAppTrnProjectListLazyQuery(
  baseOptions?: Apollo.LazyQueryHookOptions<
    AppTrnProjectListQuery,
    AppTrnProjectListQueryVariables
  >,
) {
  const options = { ...defaultOptions, ...baseOptions };
  return Apollo.useLazyQuery<
    AppTrnProjectListQuery,
    AppTrnProjectListQueryVariables
  >(AppTrnProjectListDocument, options);
}
// @ts-ignore
export function useAppTrnProjectListSuspenseQuery(
  baseOptions?: Apollo.SuspenseQueryHookOptions<
    AppTrnProjectListQuery,
    AppTrnProjectListQueryVariables
  >,
): Apollo.UseSuspenseQueryResult<
  AppTrnProjectListQuery,
  AppTrnProjectListQueryVariables
>;
export function useAppTrnProjectListSuspenseQuery(
  baseOptions?:
    | Apollo.SkipToken
    | Apollo.SuspenseQueryHookOptions<
        AppTrnProjectListQuery,
        AppTrnProjectListQueryVariables
      >,
): Apollo.UseSuspenseQueryResult<
  AppTrnProjectListQuery | undefined,
  AppTrnProjectListQueryVariables
>;
export function useAppTrnProjectListSuspenseQuery(
  baseOptions?:
    | Apollo.SkipToken
    | Apollo.SuspenseQueryHookOptions<
        AppTrnProjectListQuery,
        AppTrnProjectListQueryVariables
      >,
) {
  const options =
    baseOptions === Apollo.skipToken
      ? baseOptions
      : { ...defaultOptions, ...baseOptions };
  return Apollo.useSuspenseQuery<
    AppTrnProjectListQuery,
    AppTrnProjectListQueryVariables
  >(AppTrnProjectListDocument, options);
}
export type AppTrnProjectListQueryHookResult = ReturnType<
  typeof useAppTrnProjectListQuery
>;
export type AppTrnProjectListLazyQueryHookResult = ReturnType<
  typeof useAppTrnProjectListLazyQuery
>;
export type AppTrnProjectListSuspenseQueryHookResult = ReturnType<
  typeof useAppTrnProjectListSuspenseQuery
>;
export type AppTrnProjectListQueryResult = Apollo.QueryResult<
  AppTrnProjectListQuery,
  AppTrnProjectListQueryVariables
>;
export const SampleInputFormUpdateDocument = gql`
  mutation SampleInputFormUpdate(
    $inputSingleNumber: String
    $inputSingleNotZero: String
    $inputSingleRange: String
    $inputSingleEmail: String
    $inputMulti: String
    $inputSelectStringId: Int
  ) {
    SampleInputFormUpdate(
      inputSingleNumber: $inputSingleNumber
      inputSingleNotZero: $inputSingleNotZero
      inputSingleRange: $inputSingleRange
      inputSingleEmail: $inputSingleEmail
      inputMulti: $inputMulti
      inputSelectStringId: $inputSelectStringId
    ) {
      statusCode
      statusMessage
      errors
    }
  }
`;
export type SampleInputFormUpdateMutationFn = Apollo.MutationFunction<
  SampleInputFormUpdateMutation,
  SampleInputFormUpdateMutationVariables
>;

/**
 * __useSampleInputFormUpdateMutation__
 *
 * To run a mutation, you first call `useSampleInputFormUpdateMutation` within a React component and pass it any options that fit your needs.
 * When your component renders, `useSampleInputFormUpdateMutation` returns a tuple that includes:
 * - A mutate function that you can call at any time to execute the mutation
 * - An object with fields that represent the current status of the mutation's execution
 *
 * @param baseOptions options that will be passed into the mutation, supported options are listed on: https://www.apollographql.com/docs/react/api/react-hooks/#options-2;
 *
 * @example
 * const [sampleInputFormUpdateMutation, { data, loading, error }] = useSampleInputFormUpdateMutation({
 *   variables: {
 *      inputSingleNumber: // value for 'inputSingleNumber'
 *      inputSingleNotZero: // value for 'inputSingleNotZero'
 *      inputSingleRange: // value for 'inputSingleRange'
 *      inputSingleEmail: // value for 'inputSingleEmail'
 *      inputMulti: // value for 'inputMulti'
 *      inputSelectStringId: // value for 'inputSelectStringId'
 *   },
 * });
 */
export function useSampleInputFormUpdateMutation(
  baseOptions?: Apollo.MutationHookOptions<
    SampleInputFormUpdateMutation,
    SampleInputFormUpdateMutationVariables
  >,
) {
  const options = { ...defaultOptions, ...baseOptions };
  return Apollo.useMutation<
    SampleInputFormUpdateMutation,
    SampleInputFormUpdateMutationVariables
  >(SampleInputFormUpdateDocument, options);
}
export type SampleInputFormUpdateMutationHookResult = ReturnType<
  typeof useSampleInputFormUpdateMutation
>;
export type SampleInputFormUpdateMutationResult =
  Apollo.MutationResult<SampleInputFormUpdateMutation>;
export type SampleInputFormUpdateMutationOptions = Apollo.BaseMutationOptions<
  SampleInputFormUpdateMutation,
  SampleInputFormUpdateMutationVariables
>;
export const SampleRedisUpdateDocument = gql`
  mutation SampleRedisUpdate($storeValue: String) {
    SampleRedisUpdate(storeValue: $storeValue) {
      statusCode
      statusMessage
      errors
    }
  }
`;
export type SampleRedisUpdateMutationFn = Apollo.MutationFunction<
  SampleRedisUpdateMutation,
  SampleRedisUpdateMutationVariables
>;

/**
 * __useSampleRedisUpdateMutation__
 *
 * To run a mutation, you first call `useSampleRedisUpdateMutation` within a React component and pass it any options that fit your needs.
 * When your component renders, `useSampleRedisUpdateMutation` returns a tuple that includes:
 * - A mutate function that you can call at any time to execute the mutation
 * - An object with fields that represent the current status of the mutation's execution
 *
 * @param baseOptions options that will be passed into the mutation, supported options are listed on: https://www.apollographql.com/docs/react/api/react-hooks/#options-2;
 *
 * @example
 * const [sampleRedisUpdateMutation, { data, loading, error }] = useSampleRedisUpdateMutation({
 *   variables: {
 *      storeValue: // value for 'storeValue'
 *   },
 * });
 */
export function useSampleRedisUpdateMutation(
  baseOptions?: Apollo.MutationHookOptions<
    SampleRedisUpdateMutation,
    SampleRedisUpdateMutationVariables
  >,
) {
  const options = { ...defaultOptions, ...baseOptions };
  return Apollo.useMutation<
    SampleRedisUpdateMutation,
    SampleRedisUpdateMutationVariables
  >(SampleRedisUpdateDocument, options);
}
export type SampleRedisUpdateMutationHookResult = ReturnType<
  typeof useSampleRedisUpdateMutation
>;
export type SampleRedisUpdateMutationResult =
  Apollo.MutationResult<SampleRedisUpdateMutation>;
export type SampleRedisUpdateMutationOptions = Apollo.BaseMutationOptions<
  SampleRedisUpdateMutation,
  SampleRedisUpdateMutationVariables
>;
export const SamplePaginationDocument = gql`
  query SamplePagination($pageIndex: Int!, $pageStep: Int!) {
    SamplePagination(pageIndex: $pageIndex, pageStep: $pageStep) {
      list {
        id
        authId
        nickname
        faceImagePath
      }
      count
    }
  }
`;

/**
 * __useSamplePaginationQuery__
 *
 * To run a query within a React component, call `useSamplePaginationQuery` and pass it any options that fit your needs.
 * When your component renders, `useSamplePaginationQuery` returns an object from Apollo Client that contains loading, error, and data properties
 * you can use to render your UI.
 *
 * @param baseOptions options that will be passed into the query, supported options are listed on: https://www.apollographql.com/docs/react/api/react-hooks/#options;
 *
 * @example
 * const { data, loading, error } = useSamplePaginationQuery({
 *   variables: {
 *      pageIndex: // value for 'pageIndex'
 *      pageStep: // value for 'pageStep'
 *   },
 * });
 */
export function useSamplePaginationQuery(
  baseOptions: Apollo.QueryHookOptions<
    SamplePaginationQuery,
    SamplePaginationQueryVariables
  > &
    (
      | { variables: SamplePaginationQueryVariables; skip?: boolean }
      | { skip: boolean }
    ),
) {
  const options = { ...defaultOptions, ...baseOptions };
  return Apollo.useQuery<SamplePaginationQuery, SamplePaginationQueryVariables>(
    SamplePaginationDocument,
    options,
  );
}
export function useSamplePaginationLazyQuery(
  baseOptions?: Apollo.LazyQueryHookOptions<
    SamplePaginationQuery,
    SamplePaginationQueryVariables
  >,
) {
  const options = { ...defaultOptions, ...baseOptions };
  return Apollo.useLazyQuery<
    SamplePaginationQuery,
    SamplePaginationQueryVariables
  >(SamplePaginationDocument, options);
}
// @ts-ignore
export function useSamplePaginationSuspenseQuery(
  baseOptions?: Apollo.SuspenseQueryHookOptions<
    SamplePaginationQuery,
    SamplePaginationQueryVariables
  >,
): Apollo.UseSuspenseQueryResult<
  SamplePaginationQuery,
  SamplePaginationQueryVariables
>;
export function useSamplePaginationSuspenseQuery(
  baseOptions?:
    | Apollo.SkipToken
    | Apollo.SuspenseQueryHookOptions<
        SamplePaginationQuery,
        SamplePaginationQueryVariables
      >,
): Apollo.UseSuspenseQueryResult<
  SamplePaginationQuery | undefined,
  SamplePaginationQueryVariables
>;
export function useSamplePaginationSuspenseQuery(
  baseOptions?:
    | Apollo.SkipToken
    | Apollo.SuspenseQueryHookOptions<
        SamplePaginationQuery,
        SamplePaginationQueryVariables
      >,
) {
  const options =
    baseOptions === Apollo.skipToken
      ? baseOptions
      : { ...defaultOptions, ...baseOptions };
  return Apollo.useSuspenseQuery<
    SamplePaginationQuery,
    SamplePaginationQueryVariables
  >(SamplePaginationDocument, options);
}
export type SamplePaginationQueryHookResult = ReturnType<
  typeof useSamplePaginationQuery
>;
export type SamplePaginationLazyQueryHookResult = ReturnType<
  typeof useSamplePaginationLazyQuery
>;
export type SamplePaginationSuspenseQueryHookResult = ReturnType<
  typeof useSamplePaginationSuspenseQuery
>;
export type SamplePaginationQueryResult = Apollo.QueryResult<
  SamplePaginationQuery,
  SamplePaginationQueryVariables
>;
export const SampleRedisDocument = gql`
  query SampleRedis($sampleArgument: String!) {
    SampleRedis(sampleArgument: $sampleArgument) {
      storeValue
    }
  }
`;

/**
 * __useSampleRedisQuery__
 *
 * To run a query within a React component, call `useSampleRedisQuery` and pass it any options that fit your needs.
 * When your component renders, `useSampleRedisQuery` returns an object from Apollo Client that contains loading, error, and data properties
 * you can use to render your UI.
 *
 * @param baseOptions options that will be passed into the query, supported options are listed on: https://www.apollographql.com/docs/react/api/react-hooks/#options;
 *
 * @example
 * const { data, loading, error } = useSampleRedisQuery({
 *   variables: {
 *      sampleArgument: // value for 'sampleArgument'
 *   },
 * });
 */
export function useSampleRedisQuery(
  baseOptions: Apollo.QueryHookOptions<
    SampleRedisQuery,
    SampleRedisQueryVariables
  > &
    (
      | { variables: SampleRedisQueryVariables; skip?: boolean }
      | { skip: boolean }
    ),
) {
  const options = { ...defaultOptions, ...baseOptions };
  return Apollo.useQuery<SampleRedisQuery, SampleRedisQueryVariables>(
    SampleRedisDocument,
    options,
  );
}
export function useSampleRedisLazyQuery(
  baseOptions?: Apollo.LazyQueryHookOptions<
    SampleRedisQuery,
    SampleRedisQueryVariables
  >,
) {
  const options = { ...defaultOptions, ...baseOptions };
  return Apollo.useLazyQuery<SampleRedisQuery, SampleRedisQueryVariables>(
    SampleRedisDocument,
    options,
  );
}
// @ts-ignore
export function useSampleRedisSuspenseQuery(
  baseOptions?: Apollo.SuspenseQueryHookOptions<
    SampleRedisQuery,
    SampleRedisQueryVariables
  >,
): Apollo.UseSuspenseQueryResult<SampleRedisQuery, SampleRedisQueryVariables>;
export function useSampleRedisSuspenseQuery(
  baseOptions?:
    | Apollo.SkipToken
    | Apollo.SuspenseQueryHookOptions<
        SampleRedisQuery,
        SampleRedisQueryVariables
      >,
): Apollo.UseSuspenseQueryResult<
  SampleRedisQuery | undefined,
  SampleRedisQueryVariables
>;
export function useSampleRedisSuspenseQuery(
  baseOptions?:
    | Apollo.SkipToken
    | Apollo.SuspenseQueryHookOptions<
        SampleRedisQuery,
        SampleRedisQueryVariables
      >,
) {
  const options =
    baseOptions === Apollo.skipToken
      ? baseOptions
      : { ...defaultOptions, ...baseOptions };
  return Apollo.useSuspenseQuery<SampleRedisQuery, SampleRedisQueryVariables>(
    SampleRedisDocument,
    options,
  );
}
export type SampleRedisQueryHookResult = ReturnType<typeof useSampleRedisQuery>;
export type SampleRedisLazyQueryHookResult = ReturnType<
  typeof useSampleRedisLazyQuery
>;
export type SampleRedisSuspenseQueryHookResult = ReturnType<
  typeof useSampleRedisSuspenseQuery
>;
export type SampleRedisQueryResult = Apollo.QueryResult<
  SampleRedisQuery,
  SampleRedisQueryVariables
>;
export const UsecasesCalendarUserScheduleListDocument = gql`
  query UsecasesCalendarUserScheduleList(
    $emailList: [String!]!
    $startDate: String!
    $endDate: String!
  ) {
    UsecasesCalendarUserScheduleList(
      emailList: $emailList
      startDate: $startDate
      endDate: $endDate
    ) {
      identify
      eventId
      calendarId
      kind
      summary
      start
      end
    }
  }
`;

/**
 * __useUsecasesCalendarUserScheduleListQuery__
 *
 * To run a query within a React component, call `useUsecasesCalendarUserScheduleListQuery` and pass it any options that fit your needs.
 * When your component renders, `useUsecasesCalendarUserScheduleListQuery` returns an object from Apollo Client that contains loading, error, and data properties
 * you can use to render your UI.
 *
 * @param baseOptions options that will be passed into the query, supported options are listed on: https://www.apollographql.com/docs/react/api/react-hooks/#options;
 *
 * @example
 * const { data, loading, error } = useUsecasesCalendarUserScheduleListQuery({
 *   variables: {
 *      emailList: // value for 'emailList'
 *      startDate: // value for 'startDate'
 *      endDate: // value for 'endDate'
 *   },
 * });
 */
export function useUsecasesCalendarUserScheduleListQuery(
  baseOptions: Apollo.QueryHookOptions<
    UsecasesCalendarUserScheduleListQuery,
    UsecasesCalendarUserScheduleListQueryVariables
  > &
    (
      | {
          variables: UsecasesCalendarUserScheduleListQueryVariables;
          skip?: boolean;
        }
      | { skip: boolean }
    ),
) {
  const options = { ...defaultOptions, ...baseOptions };
  return Apollo.useQuery<
    UsecasesCalendarUserScheduleListQuery,
    UsecasesCalendarUserScheduleListQueryVariables
  >(UsecasesCalendarUserScheduleListDocument, options);
}
export function useUsecasesCalendarUserScheduleListLazyQuery(
  baseOptions?: Apollo.LazyQueryHookOptions<
    UsecasesCalendarUserScheduleListQuery,
    UsecasesCalendarUserScheduleListQueryVariables
  >,
) {
  const options = { ...defaultOptions, ...baseOptions };
  return Apollo.useLazyQuery<
    UsecasesCalendarUserScheduleListQuery,
    UsecasesCalendarUserScheduleListQueryVariables
  >(UsecasesCalendarUserScheduleListDocument, options);
}
// @ts-ignore
export function useUsecasesCalendarUserScheduleListSuspenseQuery(
  baseOptions?: Apollo.SuspenseQueryHookOptions<
    UsecasesCalendarUserScheduleListQuery,
    UsecasesCalendarUserScheduleListQueryVariables
  >,
): Apollo.UseSuspenseQueryResult<
  UsecasesCalendarUserScheduleListQuery,
  UsecasesCalendarUserScheduleListQueryVariables
>;
export function useUsecasesCalendarUserScheduleListSuspenseQuery(
  baseOptions?:
    | Apollo.SkipToken
    | Apollo.SuspenseQueryHookOptions<
        UsecasesCalendarUserScheduleListQuery,
        UsecasesCalendarUserScheduleListQueryVariables
      >,
): Apollo.UseSuspenseQueryResult<
  UsecasesCalendarUserScheduleListQuery | undefined,
  UsecasesCalendarUserScheduleListQueryVariables
>;
export function useUsecasesCalendarUserScheduleListSuspenseQuery(
  baseOptions?:
    | Apollo.SkipToken
    | Apollo.SuspenseQueryHookOptions<
        UsecasesCalendarUserScheduleListQuery,
        UsecasesCalendarUserScheduleListQueryVariables
      >,
) {
  const options =
    baseOptions === Apollo.skipToken
      ? baseOptions
      : { ...defaultOptions, ...baseOptions };
  return Apollo.useSuspenseQuery<
    UsecasesCalendarUserScheduleListQuery,
    UsecasesCalendarUserScheduleListQueryVariables
  >(UsecasesCalendarUserScheduleListDocument, options);
}
export type UsecasesCalendarUserScheduleListQueryHookResult = ReturnType<
  typeof useUsecasesCalendarUserScheduleListQuery
>;
export type UsecasesCalendarUserScheduleListLazyQueryHookResult = ReturnType<
  typeof useUsecasesCalendarUserScheduleListLazyQuery
>;
export type UsecasesCalendarUserScheduleListSuspenseQueryHookResult =
  ReturnType<typeof useUsecasesCalendarUserScheduleListSuspenseQuery>;
export type UsecasesCalendarUserScheduleListQueryResult = Apollo.QueryResult<
  UsecasesCalendarUserScheduleListQuery,
  UsecasesCalendarUserScheduleListQueryVariables
>;
